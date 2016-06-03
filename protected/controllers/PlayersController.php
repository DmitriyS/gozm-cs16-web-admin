<?php

class PlayersController extends Controller
{
    public $layout='//layouts/column1';

    public function filters()
    {
        return array();
    }

    public function actions(){
        return array(
            'captcha'=>array(
                'class'=>'CCaptchaAction',
            ),
        );
    }

    public function actionIndex()
    {
        $dataProvider=new CActiveDataProvider('Players', array(
            'criteria'=>array(
                'select' => '`id`, `rank`, `nick`, `skill`, `steam_id`, `last_seen`',
                'order' => '`skill` DESC, `id` ASC',
                'condition' => 'last_seen > ' . (time() - 60*60*24*7),
            ),
            'pagination' => array(
                'pageSize' =>  Yii::app()->config->bans_per_page,
            ),
        ));

        $model=new Players('search');
		$model->unsetAttributes();
		if (isset($_GET['Players'])) {
            $model->attributes = $_GET['Players'];
        }

        $this->render('index',array(
            'dataProvider'=>$dataProvider,
            'model'=>$model,
        ));

    }

	public function actionPlayerdetail()
	{
		if(is_numeric($_POST['id']))
		{
			$model = Players::model();
            $count = $model->count();
            $player = $model->findByPk($_POST['id']);
			if($player === null)
			{
				Yii::app()->end('alert("Ошибка!")');
			}
			$js = "$('#playerdetail-nick').html('" .  CHtml::encode($player->nick) . "');";
            $js .= "$('#playerdetail-rank').html('" . $player->rank . " из " . $count . "');";
            $js .= "$('#playerdetail-skill').html('" . $player->skill . "');";
			$js .= "$('#playerdetail-steam').html('" . $player->steam_id . "');";
			$js .= "$('#playerdetail-datetime').html('" . date('d.m.y - H:i:s',$player->last_seen) . "');";
			$js .= "$('#loading').hide();";
			$js .= "$('#viewplayer').attr({'href': '".Yii::app()->urlManager->createUrl('/players/view', array('id' => $_POST['id']))."'});";
			$js .= "$('#PlayerDetail').modal('show');";
			echo $js;
		}
		Yii::app()->end();
	}


    public function actionView($id)
    {
        $model = Players::model();
        $player = $model->findByPk($id);
        $count = $model->count();

        // Вывод всего на вьюху
        $this->render('view', array(
            'model'=>$player,
            'count'=>$count,
        ));
    }
}
