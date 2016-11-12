<?php

namespace app\controllers;

use Yii;
use yii\helpers\Json;

class UpdateController extends \yii\web\Controller
{
    public function actionIndex()
    {


        return $this->render('index');
    }


    public function actionPull()
    {
        $path = dirname(__FILE__) . '/..';
        $result = `git -C $path pull`;
	    $result = 'xx';
        return $this->render('pull',['result'=>$result]);
    }

    public function actionMigrate()
    {
        $result = `sqlite3 -init add-bonus.sql ..\db\database.db`;
        return "<pre>$result</pre>";
    }

}
