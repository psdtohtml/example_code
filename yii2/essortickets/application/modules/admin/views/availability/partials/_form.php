<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Availability */
/* @var $form yii\widgets\ActiveForm */
/* @var $type app\enums\Availability */
?>

<div class="availability-form">

    <?php Pjax::begin(['id'=>'pjax-forms']);?>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form-availability',
    ]); ?>

    <!-- Name -->
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <!-- Tour ID -->
    <?= $form->field($model, 'tour_id')->widget(Select2::classname(),
        [
            'name' => 'kv-type-01',
            'data' => $tourIds,
            'options' => [
                'placeholder' => 'Select tour ...',
                'options' => []
            ],

        ])->label('Tour Id', ['class' => 'control-label form-label-left', 'style' => 'min-width: 120px;'])
    ?>

    <!-- Type Hidden Field-->
    <?= $form->field($model, 'type')->hiddenInput(['id' => 'type' . '-' . 'hidden', 'value' => $type])->label(false) ?>

    <!-- Start -->
    <?= $form->field($model, 'start_on')->widget(\dosamigos\datepicker\DatePicker::classname(),
        [
            'size'             => 'sm',
            'containerOptions' => [
                'class' => 'form-field-middle',
            ],
            'clientOptions'    => [
                'format'    => 'yyyy-mm-dd',
                'autoclose' => true,
            ],

        ])->label('Start on', ['class' => 'control-label form-label-left'])
    ?>

    <!-- Ends -->
    <?= $form->field($model, 'ends_on')->widget(\dosamigos\datepicker\DatePicker::classname(),
        [
            'size'             => 'sm',
            'containerOptions' => [
                'class' => 'form-field-middle',
            ],
            'clientOptions'    => [
                'format'    => 'yyyy-M-dd',
                'autoclose' => true,
            ],

        ])->label('Ends on', ['class' => 'control-label form-label-left'])
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end();?>
</div>
