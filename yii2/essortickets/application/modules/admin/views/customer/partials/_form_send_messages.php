<?php

use kartik\file\FileInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Messages */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="messages-form">

    <?php Pjax::begin([
        'id'=>'pjax-forms',
    ]); ?>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form-send-email',
        'options'=>['enctype'=>'multipart/form-data'],
    ]); ?>

    <!-- head -->
    <?= $form->field($model, 'head')->textInput() ?>

    <!-- body -->
    <?= $form->field($model, 'body')->textArea(['rows' => '6']) ?>

    <!-- Attachment -->
    <?= $form->field($model, 'file')->widget(FileInput::classname(), [
        'options' => ['accept' => 'file/*'],
        'pluginOptions'=>['allowedFileExtensions'=>['pdf', 'jpg','gif','png']],
    ])?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Send' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end(); ?>

</div>
