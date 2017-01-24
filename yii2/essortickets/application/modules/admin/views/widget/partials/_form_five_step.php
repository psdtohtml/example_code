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
        <?= $form->field($model, 'country')->hiddenInput(['value'=> Yii::$app->geoData->country])->label(false) ?>

        <!-- ip -->
        <?= $form->field($model, 'ip')->hiddenInput(['value'=> $ip])->label(false) ?>

        <?php
        if ($addressVerification == 1){
            echo $form->field($model, 'street_address')->textInput();
            echo $form->field($model, 'city')->textInput();
            echo $form->field($model, 'zip')->textInput();
        }
        ?>

        <?php
        if ($subscribed == 1){
            echo $form->field($model, 'subscribed')->checkbox();
        }
        ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Next' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <?php Pjax::end(); ?>

    </div>

<?php $this->registerCss('

.form-control {
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
    -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
}
.btn-primary {
    color: #fff;
    background-color: #337ab7;
    border-color: #2e6da4;
}

.label {
    display: inline-block;
    max-width: 100%;
    margin-bottom: 5px;
    font-weight: bold;
}

.btn-success {
    color: #fff;
    background-color: #5cb85c;
    border-color: #4cae4c;
}
.btn {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: normal;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
}

.has-error .help-block, .has-error .control-label, .has-error .radio, .has-error .checkbox, .has-error .radio-inline, .has-error .checkbox-inline, .has-error.radio label, .has-error.checkbox label, .has-error.radio-inline label, .has-error.checkbox-inline label {
    color: #a94442;
}
.help-block {
    display: block;
    margin-top: 5px;
    margin-bottom: 10px;
    color: #737373;
}

.help-block-error {
    display: block;
    margin-top: 5px;
    margin-bottom: 10px;
    color: #a94442;
}


/*
	 CSS-Tricks Example
	 by Chris Coyier
	 http://css-tricks.com
*/

#page-wrap {
  width: 500px;
  margin: 100px auto;
}

.input-tiers {
  float: left;
  width: 40px;
  font: bold 20px Helvetica, sans-serif;
  padding: 3px 0 0 0;
  text-align: center;
}
form div {
  overflow: hidden;
  margin: 0 0 5px 0;
}
.button {
  margin: 0 0 0 5px;
  text-indent: -9999px;
  cursor: pointer;
  width: 29px;
  height: 29px;
  float: left;
  text-align: center;
  background: url(https://css-tricks.com/examples/InputNumberIncrementer/images/buttons.png) no-repeat;
}
.dec {
  background-position: 0 -29px;
}

.buttons {
  padding: 20px 0 0 140px;
}
');
