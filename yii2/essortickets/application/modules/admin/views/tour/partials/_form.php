<?php

use dosamigos\datepicker\DatePicker;
use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use kartik\widgets\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yiidreamteam\widgets\timezone\Picker;

/* @var $this yii\web\View */
/* @var $model app\models\Tour */
/* @var $form yii\widgets\ActiveForm */
/* @var $data app\services\User */
/* @var $recurring \app\enums\EventRecurring */
?>

<div class="tour-form">

    <?php Pjax::begin([
        'id'=>'pjax-forms',
    ]); ?>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form-tour',
        'options'=>['enctype'=>'multipart/form-data'],
    ]); ?>

    <!-- Name -->
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <!-- Logo -->
    <?= $form->field($model, 'image')->widget(FileInput::classname(), [
        'options' => ['accept' => 'image/*'],
        'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png']],
    ])?>

    <!-- Description -->
    <?= $form->field($model, 'description')->textarea([
        'rows'      => 3,
        'maxlength' => 255,
        'class'     => 'form-control form-field-middle'
    ])?>

    <!-- Manager ID -->
    <?= $form->field($model, 'manager_id')->widget(Select2::classname(),
        [
            'name' => 'kv-type-01',
            'data' => $data,
            'options' => [
                'placeholder' => 'Select manager ...',
                'options' => []
            ],

        ])->label('Tour Manager', ['class' => 'control-label form-label-left', 'style' => 'min-width: 120px;'])
    ?>

    <!-- Time Zone -->
    <?= $form->field($model, 'time_zone')->widget(Picker::className(), [
        'options' => ['class' => 'form-control'],
    ]) ?>

    <!-- Tax -->
    <?= $form->field($model, 'tax_id')->widget(Select2::classname(),
        [
            'name' => 'kv-type-01',
            'data' => $taxs,
            'options' => [
                'placeholder' => 'Select tax ...',
                'options' => []
            ],

        ])->label('Tax', ['class' => 'control-label form-label-left', 'style' => 'min-width: 120px;'])
    ?>

    <!-- Recurring -->
    <?= $form->field($model, 'recurring')->widget(Select2::classname(),
        [
            'name' => 'kv-type-01',
            'data' => $recurring,
            'options' => [
                'placeholder' => 'Select recurring ...',
                'options' => []
            ],

        ])->label('Recurring', ['class' => 'control-label form-label-left', 'style' => 'min-width: 120px;'])
    ?>

    <!-- Currency -->
    <?= $form->field($model, 'currency')->widget(Select2::classname(),
        [
            'name' => 'kv-type-01',
            'data' => \app\enums\Currency::listData(),
            'options' => [
                'placeholder' => 'Select currency ...',
                'options' => []
            ],

        ])->label('Currency', ['class' => 'control-label form-label-left', 'style' => 'min-width: 120px;'])
    ?>

    <!-- Emergencies Phone -->
    <?= $form->field($model, 'e_phone')->textInput() ?>

    <!-- Meeting Point -->
    <?= $form->field($model, 'meeting_point')->textInput() ?>

    <!-- Link Info -->
    <?= $form->field($model, 'link_info')->textInput() ?>

    <!-- Ticket Available -->
    <?= $form->field($model, 'ticket_available')->textInput() ?>

    <!-- Ticket Booking Available -->
    <?= $form->field($model, 'ticket_booking_available')->textInput() ?>

    <!-- Customer Ticket Limit -->
    <?= $form->field($model, 'customer_ticket_limit')->textInput() ?>

    <!-- Ticket Minimum -->
    <?= $form->field($model, 'ticket_minimum')->textInput() ?>

    <!-- Notice -->
    <?= $form->field($model, 'notice')->textInput(['maxlength' => true]) ?>

    <?php if ($model->isNewRecord){
        echo $form->field($model, 'start_tour_date')->widget(DatePicker::className(), [
            'language' => 'en',
            'size' => 'md',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
        ])->label('Start Tour Date');

        echo $form->field($model, 'end_tour_date')->widget(DatePicker::className(), [
            'language' => 'en',
            'size' => 'md',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
        ])->label('End Tour Date');
    }
    ?>

    <?= $form->field($model, 'start_tour_time')->widget(TimePicker::className(), [
        'name' => 't1',
        'pluginOptions' => [
            'showMeridian' => false,
            'minuteStep' => 5,
        ]
    ])->label('Start Tour Time')?>

    <?= $form->field($model, 'end_tour_time')->widget(TimePicker::className(), [
        'name' => 't1',
        'pluginOptions' => [
            'showMeridian' => false,
            'minuteStep' => 5,
        ]
    ])->label('End Tour Time')?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end(); ?>

</div>
