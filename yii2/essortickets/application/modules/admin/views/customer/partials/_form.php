<?php

use dosamigos\datepicker\DatePicker;
use kartik\widgets\Select2;
use kartik\widgets\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yiidreamteam\widgets\timezone\Picker;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="customer-form">

    <?php Pjax::begin([
        'id'=>'pjax-forms',
    ]); ?>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form-customer',
    ]); ?>

    <!-- title -->
    <?= $form->field($model, 'subscribed')->dropDownList(
        [
            \app\enums\TitleCustomer::MS => \app\enums\TitleCustomer::labelById(\app\enums\TitleCustomer::MS),
            \app\enums\TitleCustomer::MISS => \app\enums\TitleCustomer::labelById(\app\enums\TitleCustomer::MISS),
            \app\enums\TitleCustomer::MRS => \app\enums\TitleCustomer::labelById(\app\enums\TitleCustomer::MRS),
            \app\enums\TitleCustomer::MR => \app\enums\TitleCustomer::labelById(\app\enums\TitleCustomer::MR),
        ],
        [
            'id' => 'form-title-drop',
            'class'=>'form-control',
            'prompt' => 'Select title...'
        ])->label('Title') ?>

    <!-- name -->
    <?= $form->field($model, 'name')->textInput() ?>

    <!-- last_name -->
    <?= $form->field($model, 'last_name')->textInput() ?>

    <!-- email -->
    <?= $form->field($model, 'email')->textInput() ?>

    <!-- phone -->
    <?= $form->field($model, 'phone')->textInput() ?>

    <!-- company -->
    <?= $form->field($model, 'company')->textInput() ?>

    <!-- country -->
    <?= $form->field($model, 'country')->widget(Select2::classname(),
        [
            'name' => 'kv-type-01',
            'data' => $countries,
            'options' => [
                'placeholder' => 'Select country ...',
                'options' => []
            ],

        ])->label('Country')
    ?>

    <!-- city -->
    <?= $form->field($model, 'city')->textInput() ?>

    <!-- street_address -->
    <?= $form->field($model, 'street_address')->textInput() ?>

    <!-- zip -->
    <?= $form->field($model, 'zip')->textInput() ?>

    <!-- subscribed -->
    <?= $form->field($model, 'subscribed')->dropDownList([\app\enums\YesNo::NO => \app\enums\YesNo::labelById(\app\enums\YesNo::NO), \app\enums\YesNo::YES => \app\enums\YesNo::labelById(\app\enums\YesNo::YES)],
        [
            'id' => 'form-subscribed-drop',
            'class'=>'form-control',
            'prompt' => 'Select status...'
        ])->label('Subscribed') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end(); ?>

</div>
