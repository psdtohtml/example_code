<?php

use kartik\widgets\Select2;
use kartik\widgets\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\TimeDetail */
/* @var $modelDetail app\models\Detail */
/* @var $form yii\widgets\ActiveForm */
/* @var integer $availabilityId */
/* @var $usersIds app\services\User */
?>

<div class="availability-form">

    <?php Pjax::begin(['id'=>'pjax-forms']);?>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form-add-time',
    ]); ?>

    <!-- Availability ID -->
    <?= $form->field($model, 'availability_id')->hiddenInput(['id' => 'availability-id' . '-' . 'hidden', 'value' => $availabilityId])->label(false) ?>

    <!-- Time -->
    <?= $form->field($model, 'time')->widget(TimePicker::classname(),
    [
        'name' => 't1',
        'pluginOptions' => [
        'showMeridian' => false,
        'minuteStep' => 1,
    ]
    ]) ?>

    <!-- Name -->
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <!-- Capacity -->
    <?= $form->field($model, 'capacity')->textInput(['maxlength' => true]) ?>

    <!-- Assigned -->
    <?= $form->field($model, 'assigned')->widget(Select2::classname(),
        [
            'name' => 'kv-type-01',
            'data' => $usersIds,
            'options' => [
                'placeholder' => 'Select Guide/User ...',
                'options' => []
            ],

        ])->label('Assigned Guide/User', ['class' => 'control-label form-label-left', 'style' => 'min-width: 120px;'])
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end();?>
</div>
