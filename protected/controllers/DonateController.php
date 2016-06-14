<?php

class DonateController extends Controller
{
    public $layout='//layouts/column1';

    public function filters()
    {
        return array();
    }

    public function actionIndex()
    {
        $this->render('index');
    }
}
