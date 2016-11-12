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
	    if(preg_match('!Already up-to-date.!',$result)){
		    $msg =['class'=>'info', 'msg'=>'Обновлений нет'];
		}else{
		    $msg =['class'=>'success', 'msg'=>'Успешно обновлено'];
	    }
	    $info = `git show`;
	    preg_match('!\n\n(.*)\n\n!',$info,$arr);
	    print $arr[1];
        return $this->render('pull',['msg'=>$msg, 'version'=>$arr[1], 'result'=>$result]);
    }

    public function actionMigrate()
    {
        $result = `sqlite3 -init add-bonus.sql ..\db\database.db`;
        return "<pre>$result</pre>";
    }

}
