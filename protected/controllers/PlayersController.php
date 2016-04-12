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
			$model = Players::model()->findByPk($_POST['id']);
			if($model === null)
			{
				Yii::app()->end('alert("Ошибка!")');
			}
			$js = "$('#bandetail-nick').html('" .  CHtml::encode($model->nick) . "');";
            $js .= "$('#bandetail-rank').html('" . $model->rank . "');";
            $js .= "$('#bandetail-skill').html('" . $model->skill . "');";
			$js .= "$('#bandetail-steam').html('" . $model->steam_id . "');";
			$js .= "$('#bandetail-datetime').html('" . date('d.m.y - H:i:s',$model->last_seen) . "');";
			$js .= "$('#loading').hide();";
			$js .= "$('#viewban').attr({'href': '".Yii::app()->urlManager->createUrl('/players/view', array('id' => $_POST['id']))."'});";
			$js .= "$('#BanDetail').modal('show');";
			echo $js;
		}
		Yii::app()->end();
	}


    public function actionView($id)
    {
        $model=Players::model()->findByPk($id);

        // Вывод всего на вьюху
        $this->render('view', array(
            'model'=>$model,
        ));
    }

}
