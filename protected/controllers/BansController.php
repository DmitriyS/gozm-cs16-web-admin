<?php
/**
 * Контррллер банов
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */
class BansController extends Controller
{
	public $layout='//layouts/column1';

	public function filters()
	{
		return array(
			'accessControl',
			'postOnly + delete',
			'ajaxOnly + bandetail, unban'
		);
	}

	public function actions(){
        return array(
            'captcha'=>array(
                'class'=>'CCaptchaAction',
            ),
        );
    }

	public function actionUnban($id)
	{
        $ban_model = $this->loadModel($id);

		if (!Webadmins::checkAccess('bans_unban', $ban_model->admin_nick)) {
            throw new CHttpException(403, "У Вас недостаточно прав");
        }

        $history_model = new History;
        $history_model->unsetAttributes();
        $history_model->player_ip = $ban_model->player_ip;
        $history_model->player_id = $ban_model->player_id;
        $history_model->player_nick = $ban_model->player_nick;
        $history_model->admin_ip = $ban_model->admin_ip;
        $history_model->admin_id = $ban_model->admin_id;
        $history_model->admin_nick = $ban_model->admin_nick;
        $history_model->ban_type = $ban_model->ban_type;
        $history_model->ban_reason = $ban_model->ban_reason;
        $history_model->ban_created = $ban_model->ban_created;
        $history_model->ban_length = $ban_model->ban_length;
        $history_model->server_ip = $ban_model->server_ip;
        $history_model->server_name = $ban_model->server_name;
        $history_model->unban_created = time();
        $history_model->unban_reason = 'Разбанен с сайта';
        $history_model->unban_admin_nick = Yii::app()->user->name;

        if ($history_model->save()) {
    		if ($ban_model->delete()) {
                Yii::app()->end('Игрок разбанен');
            }
        }

        Yii::app ()->end(CHtml::errorSummary($ban_model));
	}

	/**
	 * Вывод инфы о бане
	 * @param integer $id ID бана
	 */
	public function actionView($id)
	{
		// Подгружаем комментарии и файлы
		$files = new Files;
		//$this->performAjaxValidation($files);
		$files->unsetAttributes();
		$comments = new Comments;
		$comments->unsetAttributes();

		// Подгружаем баны
		$model=Bans::model()->with('admin')->findByPk($id);
		if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        $geo = false;
        // Проверка прав на просмотр IP
        $ipaccess = Webadmins::checkAccess('ip_view');
        if($ipaccess) {
            $geo = array(
                'city' => 'Не определен',
                'region' => 'Не определен',
                'country' => 'Не определена',
                'lat' => 0,
                'lng' => 0,
                'zoom' => 10,
            );
			$get = @file_get_contents('http://ru.sxgeo.city/xml/' . $model->player_ip);
            if($get) {
                $xml = @simplexml_load_string($get);
                if(!empty($xml->ip)) {
                    $geo['city'] = isset($xml->ip->city->name_ru) ? $xml->ip->city->name_ru : $geo['city'];
                    $geo['region'] = isset($xml->ip->region->name_ru) ? $xml->ip->region->name_ru : $geo['region'];
                    $geo['country'] = isset($xml->ip->country->name_ru) ? $xml->ip->country->name_ru : $geo['country'];
                    $geo['lat'] = isset($xml->ip->city->lat) ? $xml->ip->city->lat : $geo['lat'];
                    $geo['lng'] = isset($xml->ip->city->lon) ? $xml->ip->city->lon : $geo['lng'];
                }
            }

            if($geo['lat'] == 0 && $geo['lng'] == 0)
            {
                if($geo['country'] !== 'Не определена')
                {
                    $lat_lng = Yii::app()->CountryToLatLng->lookup($geo['country']);
                    $geo['lat'] = $lat_lng['lat'];
                    $geo['lng'] = $lat_lng['lng'];
                    $geo['zoom'] = $lat_lng['zoom'];
                }
            }
        }

		// Добавление файла
		if(isset($_POST['Files']) && !empty($_POST['Files']['name'])) {
			// Задаем аттрибуты
			$files->attributes = $_POST['Files'];
			$files->bid = intval($id);
			$files->save();
			$this->refresh();
		}

		// Добавление комментария
		if(isset($_POST['Comments'])) {
			//exit(print_r($_POST['Comments']));
			$comments->attributes = $_POST['Comments'];
			$comments->bid = $id;
			if ($comments->save()) {
                $this->refresh();
            }
        }

		// Выборка комментариев
		$c = new CActiveDataProvider($comments, array(
			'criteria' => array(
				'condition' => 'bid = :bid',
				'params' => array(
					':bid' => $id
				),
			),
		));

		// Выборка файлов
		$f = new CActiveDataProvider(Files::model(), array(
			'criteria' => array(
				'condition' => 'bid = :bid',
				'params' => array(
					':bid' => $id
				),
			),
		));

		// История банов
		$history = new CActiveDataProvider('History', array(
			'criteria' => array(
				'condition' => '`player_ip` = :hip OR `player_id` = :hid',
				'params' => array(
					':hip' => $model->player_ip,
					':hid' => $model->player_id
				),
			),
			'pagination' => array(
				'pageSize' => 5
			)
		));

		// Вывод всего на вьюху
		$this->render('view',array(
			'geo' => $geo,
			'ipaccess' => $ipaccess,
			'model'=>$model,
			'files' => $files,
			'comments'=> $comments,
			'f' => $f,
			'c' => $c,
			'history' => $history
		));
	}

	/**
	 * Добавить бан
	 */
	public function actionCreate()
	{
		// Проверка прав
		if (!Webadmins::checkAccess('bans_add')) {
            throw new CHttpException(403, "У Вас недостаточно прав");
        }

        $model=new Bans;

		// Аякс проверка формы
		$this->performAjaxValidation($model);

		if(isset($_POST['Bans'])) {
			$model->attributes=$_POST['Bans'];
			$model->admin_nick = 'Web';
			$model->server_name = 'Забанен с сайта';
			if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->bid));
            }
        }

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Редактировать бан
	 * @param integer $id ID бана
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Проверка прав
		if (!Webadmins::checkAccess('bans_edit', $model->admin_nick)) {
            throw new CHttpException(403, "У Вас недостаточно прав");
        }

        // Аякс проверка формы
		// $this->performAjaxValidation($model);

		// Сохраняем форму
		if(isset($_POST['Bans'])) {
			$model->attributes=$_POST['Bans'];
			if(isset($_POST['selfreasoncheckbox']) && isset($_POST['self_ban_reason'])) {
				$model->ban_reason = $_POST['self_ban_reason'];
			}
			if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->bid));
            }
        }

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Удаление бана
	 * @param integer $id ID бана
	 */
	public function actionDelete($id)
	{
		$model = $this->loadModel($id);

		// Проверка прав
		if (!Webadmins::checkAccess('bans_delete', $model->admin_nick)) {
            throw new CHttpException(403, "У Вас недостаточно прав");
        }

        $model->delete();
		// Если не аякс запрос, то редиректим
		if (!isset($_GET['ajax'])) {
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
    }

	/**
	 * Вывод всех банов
	 */
	public function actionIndex()
	{
		if(Yii::app()->request->isAjaxRequest && isset($_POST['server'])) {
			if($_POST['server'] == 0) {
				Yii::app()->end('$("#Bans_admin_nick").html("<option value=\"0\">Не выбрано</option>");');
			}
			$amxadmins = Amxadmins::model()->with('servers')->findAll('`address` = :addr', array(':addr' => $_POST['server']));

			$js = "<option>Любой админ</option>";
			foreach($amxadmins as $admin) {
				$js .= "<option value=\"{$admin->steamid}\">{$admin->nickname}</option>";
			}

			Yii::app()->end("$('#Bans_admin_nick').html('{$js}')");
		}

		$model=new Bans('search');
		$model->unsetAttributes();
		if (isset($_GET['Bans'])) {
            $model->attributes = $_GET['Bans'];
        }

        $select = "((ban_created+(ban_length*60)) > UNIX_TIMESTAMP() OR ban_length = 0) AND `expired` = 0";

		$dataProvider=new CActiveDataProvider('Bans', array(
			'criteria'=>array(
				'condition' => Yii::app()->config->auto_prune
					?
				$select
					:
				null,
				'order' => '`ban_created` DESC',
            ),
			'pagination' => array(
				'pageSize' =>  Yii::app()->config->bans_per_page,

			),)
		 );

		// Проверяем IP посетителя, есть ли он в активных банах
		$check = Bans::model()->count(
			"`player_ip` = :ip AND " . $select,
			array(
				':ip'=> Prefs::getRealIp(),
			)
		);
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'model'=>$model,
			'check' => $check > 0 ? true : false,
		));

	}

	/**
	 * Управление банами (Хз, буду ли использовать)
	 */
	public function actionAdmin()
	{
		if (Yii::app()->user->isGuest) {
            throw new CHttpException(403, "У Вас недостаточно прав");
        }

        $model=new Bans('search');
		$model->unsetAttributes();
		if (isset($_GET['Bans'])) {
            $model->attributes = $_GET['Bans'];
        }

        $this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Вывод данных о бане в модальке
	 */
	public function actionBandetail()
	{
		if(is_numeric($_POST['bid']))
		{
			$model = Bans::model()->with('admin')->findByPk($_POST['bid']);
			if($model === null)
			{
				Yii::app()->end('alert("Ошибка!")');
			}
			$js = "$('#bandetail-nick').html('" .  CHtml::encode($model->player_nick) . "');";
			$js .= "$('#bandetail-steam').html('" . $model->player_id . "');";
			//$js .= "$('#bandetail-steamcommynity').html('" . Prefs::steam_convert($model->player_id, true) . "');";
			$js .= "$('#bandetail-ip').html('" . (Webadmins::checkAccess('ip_view') ? $model->player_ip : 'Cкрыт') . "');";
			//$js .= "$('#bandetail-type').html('" . Prefs::getBanType($model->ban_type) . "');";
			$js .= "$('#bandetail-datetime').html('" . date('d.m.y - H:i:s',$model->ban_created) . "');";
			$js .= "$('#bandetail-expired').html('" . ($model->ban_length == '-1' ?
				'Разбанен' :
				Prefs::date2word($model->ban_length) . ($model->expired == 1 ? ' (истек)' : '')) . "');";
            $js .= "$('#bandetail-map').html('" .  $model->map_name . "');";
            $js .= "$('#bandetail-reason').html('" . CHtml::encode($model->ban_reason) . "');";
			$js .= "$('#bandetail-admin').html('" . CHtml::encode($model->admin_nick) . "');";
			$js .= "$('#bandetail-server').html('" . CHtml::encode($model->server_name) . "');";
			//$js .= "$('#bandetail-kicks').html('" . $model->ban_kicks . "');";
			$js .= "$('#loading').hide();";
			$js .= "$('#viewban').attr({'href': '".Yii::app()->urlManager->createUrl('/bans/view', array('id' => $_POST['bid']))."'});";
			$js .= "$('#BanDetail').modal('show');";
			echo $js;
		}
		Yii::app()->end();
	}

	public function actionMotd($sid, $adm = 0, $lang = 'ru')
	{
		$this->layout = FALSE;

		$sid = (int)SubStr( $sid, 1 );

		$model = Bans::model()->findByPk($sid);
		if ($model === null) {
            Yii::app()->end('Error!');
        }

        $this->render('motd', array(
			'model'=>$model,
			'show_admin' => $adm == 1 ? TRUE : FALSE
		));
	}

	/**
	 * Загрузка модели по ID
	 * @param integer ID бана
	 */
	public function loadModel($id)
	{
		$model=Bans::model()->findByPk($id);
		if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
	}

	/**
	 * Аякс проверка формы
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='bans-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
