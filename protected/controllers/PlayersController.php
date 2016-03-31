<?php

class PlayersController extends Controller
{
    public $layout='//layouts/column1';

    public function filters()
    {
        return array(
        );
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
                'order' => '`id` ASC',
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

}
