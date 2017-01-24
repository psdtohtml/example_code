<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dataProviderExtras yii\data\ActiveDataProvider */
/* @var $dataProviderQuestion yii\data\ActiveDataProvider */
/* @var $dataProviderVariant yii\data\ActiveDataProvider */

$this->title = 'Tickets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('partials/_tiers', [
        'dataProvider' => $dataProvider,
        'model' => $model,
        'tourId' => $tourId,
    ]) ?>

    <?= $this->render('partials/_extras', [
        'dataProvider' => $dataProviderExtras,
        'model' => $modelExtras,
    ]) ?>

    <?= $this->render('partials/_variant', [
        'dataProvider' => $dataProviderVariant,
        'tourId' => $tourId,
    ]) ?>

    <?= $this->render('partials/_question', [
        'dataProvider' => $dataProviderQuestion,
    ]) ?>

</div>
