<?php
/* @var $model object */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\HtmlPurifier;
?>
<div class="site-news">
    <a href="<?= Url::to(['site/news', 'id' => $model->id]); ?>"><div class="info__title"><?= Html::encode($model->title) ?></div></a>
    <span><?= Yii::$app->formatter->asDate($model->created_at) ?></span>
    <div class="info__text"><?= HtmlPurifier::process(substr($model->content, 0, 550)) . '...' ?></div>
    <hr>
</div>