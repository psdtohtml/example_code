<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EmailTemplates */

$this->title = 'Редактировани шаблона: "' . $model->name . '"';
?>
<div class="box box-warning">
    <div class="box-header">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>
</div>
