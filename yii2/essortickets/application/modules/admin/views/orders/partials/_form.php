<?php

use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Orders */
/* @var $form yii\widgets\ActiveForm */
/* @var $managers app\services\User */
?>

<div class="booking-form">

    <?php $form = ActiveForm::begin(); ?>

    <!-- Manager ID -->
    <?= $form->field($model, 'user_id')->widget(Select2::classname(),
        [
            'name' => 'kv-type-01',
            'data' => $managers,
            'options' => [
                'placeholder' => 'Select manager ...',
                'options' => []
            ],

        ])->label('Tour Manager', ['class' => 'control-label form-label-left', 'style' => 'min-width: 120px;'])
    ?>

    <!-- Starts On -->
    <?= $form->field($model, 'datetime_booking')->widget(DateTimePicker::classname(),
        [
            'name' => 'datetime_10',
            'options' => ['placeholder' => 'Date Booked ...'],
            'convertFormat' => true,
            'pluginOptions' => [
                'format' => 'yyyy-M-dd HH:i',
                'startDate' => date('Y-m-d H:i:s', time()),
                'todayHighlight' => true
            ]
        ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
