<?php

use kartik\widgets\ColorInput;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Channel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="channel-form">

    <?php $form = ActiveForm::begin([
        'action' => Url::to(['/admin/channel/manage-channel','id'=> $model->id]),
    ]); ?>

    <!-- Layout -->
    <?= $form->field($model, 'layout')->widget(Select2::classname(),
        [
            'name' => 'kv-type-01',
            'data' => $layout,
            'options' => [
                'placeholder' => 'Select layout ...',
                'options' => []
            ],

        ])->label('Layout')
    ?>
    <!-- Event View Days -->
    <?= $form->field($model, 'event_view_days')->textInput(['maxlength' => true]) ?>
    <!-- Color -->
    <?= $form->field($model, 'color')->widget(ColorInput::classname(), [
        'options' => ['placeholder' => 'Select color ...'],
    ]);
    ?>
    <!-- Button Label -->
    <?= $form->field($model, 'button_label')->textInput(['maxlength' => true]) ?>
    <!-- Height -->
    <?= $form->field($model, 'height')->textInput(['maxlength' => true]) ?>
    <!-- Width -->
    <?= $form->field($model, 'width')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'subscribed')->checkbox() ?>

    <?= $form->field($model, 'address')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
