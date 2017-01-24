<?php

use dosamigos\datepicker\DatePicker;
use kartik\widgets\Select2;
use kartik\widgets\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Variant */
/* @var $data app\models\Variant */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="variant-form">

    <?php Pjax::begin([
        'id'=>'pjax-forms',
    ]); ?>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form-variant',
    ]); ?>

    <?= $form->field($model, 'tour_id')->hiddenInput(['id' => 'tour-id' . '-' . 'hidden', 'value' => $tourId])->label(false) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end(); ?>

</div>
