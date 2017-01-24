<?php

use yii\helpers\Html;
use yii\widgets\DetailView;;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\rebate\BalanceForm */
/* @var $user common\models\User */

$this->title = 'Пожалуйста внимательно проверьте введенные данные!';
?>
<div class="box box-warning">
    <div class="box-header">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fullName')->textInput(['readonly' => true]) ?>

        <?= $form->field($model, 'login')->textInput(['readonly' => true]) ?>

        <?= $form->field($model, 'balance')->textInput(['readonly' => true]) ?>

        <?= $form->field($model, 'amount')->textInput(['readonly' => true]) ?>

        <?= $form->field($model, 'payment_detail')->textInput(['readonly' => true]) ?>

        <?= Html::submitButton('Подтвердить', ['class' => 'btn btn-primary']) ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
