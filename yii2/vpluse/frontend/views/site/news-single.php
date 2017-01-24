<?php

/* @var $this yii\web\View */
/* @var $news object */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Новости';
$this->params['breadcrumbs'][] = ['label' => 'Все новости', 'url' => [Url::to(['site/news'])]];
$this->params['breadcrumbs'][] = $news->title;

?>

<div class="site-news">
    <div class="info__title"><?= Html::encode($news->title) ?></div>
    <p><?= Yii::$app->formatter->asDate($news->created_at) ?></p>
    <div class="info__text"><?= $news->content ?></div>
</div>
