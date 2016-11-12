<?php

namespace app\controllers;

use Yii;
use app\models\Shift;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class ArchiveController extends Controller
{
    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Shift::find()->where(['not', ['finished_at' => null]])->orderBy(['started_at' => SORT_DESC]),
            'sort' => [
                'attributes' => ['']
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $shift = Shift::findOne(['id' => $id]);
        $masters = $shift->masters;
        usort($masters, function($a, $b) use ($shift) {
            return ($a->getTime($shift->id) . $a->id) > ($b->getTime($shift->id) . $b->id);
        });
        $haircutTable = $shift->getHaircutTable(true);
        $totalTable = $shift->totalTable;
        return $this->render('view', [
            'shift' => $shift,
            'masters' => $masters,
            'haircutTable' => $haircutTable,
            'totalTable' => $totalTable
        ]);
    }
}
