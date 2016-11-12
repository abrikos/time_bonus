<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Тайм',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Главная', 'url' => ['/site'], 'visible' => Yii::$app->controller->id != 'site'],
            [
                'label' => 'Справочники',
                'items' => [
                    ['label' => 'Администраторы', 'url' => '/administrator'],
                    ['label' => 'Мастера', 'url' => '/master'],
                    ['label' => 'Склад', 'url' => '/storage'],
                    ['label' => 'Бонус-карты', 'url' => '/card']
                ],
            ],
            ['label' => 'Архив', 'url' => ['/archive']],
            ['label' => 'Обновление', 'url' => ['/update/pull']],
            ['label' => 'Графики', 'url' => ['/graphs'], 'visible' => false],
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Тайм <?= date('Y') ?></p>

        <p class="pull-right">Разработка <a href="http://bbgroup.pro" target="_blank">BBSoft</a></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
