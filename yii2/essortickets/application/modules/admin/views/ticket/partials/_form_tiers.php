<?php

use app\enums\FeeType;
use kartik\widgets\Select2;
use xj\bootbox\BootboxAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Ticket */
/* @var $form yii\widgets\ActiveForm */
/* @var integer $tourId */

?>

<div class="ticket-form">

    <?php Pjax::begin([
        'id'=>'pjax-forms',
    ]); ?>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form-tiers',
    ]); ?>

    <?= $form->field($model, 'tour_id')->hiddenInput(['id' => 'tour-id' . '-' . 'hidden', 'value' => $tourId])->label(false) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <!-- FEE TYPE -->
    <?= $form->field($model, 'booking_fee_type')->widget(Select2::classname(),
        [
            'name' => 'kv-type-01',
            'data' => [FeeType::FEE_TYPE_FIXED => 'Fixed', FeeType::FEE_TYPE_PERCENTAGE => 'Percentage'],
            'options' => [
                'placeholder' => 'Select type ...',
                'options' => []
            ],

        ])->label('Booking Fee Type', ['class' => 'control-label form-label-left', 'style' => 'min-width: 120px;'])
    ?>

    <?= $form->field($model, 'booking_fee')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end();?>
</div>

