<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Channel */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Channels', 'url' => ['manage']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="channel-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="form-part-right sidebar-465">
        <div class="form-group">
            <div class="form-group well">
                <?= $this->render('partials/_settings', [
                    'model'         => $model,
                    'layout'        => $layout,
                ]); ?>
            </div>
            <div class="form-group well">
                <?= $this->render('partials/_widget_code', [
                    'model'         => $model,
                ]); ?>
            </div>
            <div class="form-group well">
                <?= $this->render('partials/_preview', [
                    'model'         => $model,
                ]); ?>
            </div>
        </div>
    </div>

</div>
