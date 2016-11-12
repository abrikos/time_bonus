<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Архив';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="archive-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
        	'id',
            'administratorArrive',
            'finishedAt',
            'clientCount',
            'total',
            'final_cash',

            [
            	'class' => 'yii\grid\ActionColumn',
            	'template' => '{view}'
            ]
        ],
    ]); ?>

</div>
