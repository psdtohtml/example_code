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

?>

<div class="extras-form">

    <?php Pjax::begin(['id'=>'pjax-forms']);?>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form-extras',
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

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end();?>
</div>