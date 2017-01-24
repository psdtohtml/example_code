<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Availabilities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="availability-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('partials/_season', [
        'dataProvider' => $dataProviderSeasons,
    ]) ?>

    <?= $this->render('partials/_variation', [
        'dataProvider' => $dataProviderVariation,
    ]) ?>

</div>
