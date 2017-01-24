<?php

use yii\helpers\Html;
use yii\widgets\DetailView;;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\rebate\BalanceForm */
/* @var $user common\models\User */

$this->title = 'Ввод начисления пользователю ' . $user->username;
?>
<div class="box box-warning">
    <div class="box-header">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'balance')->textInput() ?>

        <?= $form->field($model, 'company_id')->dropDownList($model->companyNamesList(), ['promt'=>'Все']) ?>

        <?= $form->field($model, 'note')->textInput() ?>

        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
