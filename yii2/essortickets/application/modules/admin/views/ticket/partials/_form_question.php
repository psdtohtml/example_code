<?php

use kartik\widgets\Select2;
use xj\bootbox\BootboxAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Extras */
/* @var $form yii\widgets\ActiveForm */
/* @var $ticketIds app\services\Ticket */
/* @var $inputTypes app\services\Question */

?>

<div class="extras-form">

    <?php Pjax::begin(['id'=>'pjax-forms']);?>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form-question',
    ]); ?>

    <!-- Ticket Id -->
    <?= $form->field($model, 'ticket_id')->widget(Select2::classname(),
        [
            'name' => 'kv-type-01',
            'data' => $ticketIds,
            'options' => [
                'placeholder' => 'Select tiers ...',
                'options' => []
            ],

        ])->label('Tiers')
    ?>

    <?= $form->field($model, 'question')->textInput(['maxlength' => true]) ?>

    <!-- Input type -->
    <?= $form->field($model, 'input_type')->widget(Select2::classname(),
        [
            'name' => 'kv-type-01',
            'data' => $inputTypes,
            'options' => [
                'placeholder' => 'Select input type ...',
                'options' => []
            ],

        ])->label('Input Type')
    ?>

    <?= $form->field($model, 'optional')->checkbox() ?>

    <?= $form->field($model, 'aggregate')->checkbox() ?>

    <?= $form->field($model, 'option')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end();?>
</div>