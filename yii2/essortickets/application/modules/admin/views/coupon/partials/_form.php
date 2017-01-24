<?php

use app\enums\FeeType;
use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Coupon */
/* @var $form yii\widgets\ActiveForm */
/* @var $ticketIds app\services\Ticket */
?>

<div class="coupon-form">

    <?php Pjax::begin([
        'id'=>'pjax-forms',
    ]); ?>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form-coupon',
    ]); ?>

    <!-- Ticket ID -->
    <?= $form->field($model, 'ticket_id')->widget(Select2::classname(),
        [
            'name' => 'kv-type-01',
            'data' => $ticketIds,
            'options' => [
                'placeholder' => 'Select ticket ...',
                'options' => []
            ],

        ])->label('Ticket ID', ['class' => 'control-label'])
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'limit')->textInput() ?>

    <!-- Starts On -->
    <?= $form->field($model, 'starts_on')->widget(DateTimePicker::classname(),
        [
            'name' => 'datetime_10',
            'options' => ['placeholder' => 'Select starts on ...'],
            'convertFormat' => true,
            'pluginOptions' => [
                'format' => 'yyyy-M-dd hh:i',
                'startDate' => '2016-01-01 12:00',
                'todayHighlight' => true
            ]
        ]) ?>

    <!-- Ends On -->
    <?= $form->field($model, 'ends_on')->widget(DateTimePicker::classname(),
        [
            'name' => 'datetime_10',
            'options' => ['placeholder' => 'Select ends on ...'],
            'convertFormat' => true,
            'pluginOptions' => [
                'format' => 'yyyy-M-dd hh:i',
                'startDate' => '2016-01-01 12:00',
                'todayHighlight' => true
            ]
        ]) ?>

    <!-- Valid From -->
    <?= $form->field($model, 'valid_from')->widget(DateTimePicker::classname(),
        [
            'name' => 'datetime_10',
            'options' => ['placeholder' => "Select valid ..."],
            'convertFormat' => true,
            'pluginOptions' => [
                'format' => 'yyyy-M-dd hh:i',
                'startDate' => '2016-01-01 12:00',
                'todayHighlight' => true
            ]
        ]) ?>

    <!-- Expires On -->
    <?= $form->field($model, 'expires_on')->widget(DateTimePicker::classname(),
        [
            'name' => 'datetime_10',
            'options' => ['placeholder' => "Select expires ..."],
            'convertFormat' => true,
            'pluginOptions' => [
                'format' => 'yyyy-M-dd hh:i',
                'startDate' => '2016-01-01 12:00',
                'todayHighlight' => true
            ]
        ]) ?>

    <!-- Discount Type -->
    <?= $form->field($model, 'discount_type')->widget(Select2::classname(),
        [
            'name' => 'kv-type-01',
            'data' => [FeeType::FEE_TYPE_FIXED => 'Fixed', FeeType::FEE_TYPE_PERCENTAGE => 'Percentage'],
            'options' => [
                'placeholder' => 'Select type ...',
                'options' => []
            ],

        ])->label('Discount Type', ['class' => 'control-label form-label-left', 'style' => 'min-width: 120px;'])
    ?>

    <?= $form->field($model, 'discount')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end();?>
</div>
