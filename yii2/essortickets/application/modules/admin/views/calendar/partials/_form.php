<?php

use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;
use kartik\widgets\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Events */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="events-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="form-group well">

        <!-- Guide -->
        <?= $form->field($model, 'guide_id')->widget(Select2::classname(),
            [
                'name' => 'kv-type-01',
                'data' => $users,
                'options' => [
                    'placeholder' => 'Select guide ...',
                    'options' => []
                ],

            ])->label('Guide')
        ?>

        <!-- Product -->
        <?= $form->field($model, 'title')->widget(Select2::classname(),
            [
                'name' => 'kv-type-01',
                'data' => $products,
                'options' => [
                    'placeholder' => 'Select product ...',
                    'options' => []
                ],

            ])->label('Product')
        ?>

        <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

        <!-- Event start date -->
        <?= $form->field($model, 'start_date')->widget(DatePicker::classname(),
            [
                'name' => 'datetime_10',
                'options' => ['placeholder' => 'Select start date ...'],
                'convertFormat' => true,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-M-dd',
                    'startDate' => '2015-11-11',
                    'todayHighlight' => false
                ]
            ])->label('Event start date')
        ?>

        <!-- Event start time -->
        <?= $form->field($model, 'start_time')->widget(TimePicker::classname(),
            [
                'name' => 't1',
                'pluginOptions' => [
                    'showMeridian' => false,
                    'minuteStep' => 5,
                ]
            ])->label('Event start time')
        ?>

        <!-- Size All -->
        <?= $form->field($model, 'size_all')->textInput(['maxlength' => true]) ?>

        <!-- Event status -->
        <?= $form->field($model, 'dayoff')->widget(Select2::classname(),
            [
                'name' => 'kv-type-01',
                'data' => [0 => 'ON', 1 => 'OFF'],
                'options' => [
                    'placeholder' => 'Select status ...',
                    'options' => []
                ],

            ])->label('Event status')
        ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
