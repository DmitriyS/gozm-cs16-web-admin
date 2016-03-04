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
            ),)
         );

        $this->render('index',array(
            'dataProvider'=>$dataProvider,
        ));

    }

}
