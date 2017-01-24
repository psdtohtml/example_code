<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\rebate\PaymentChangeForm */

$this->title = $model->payment_detail ? 'Изменение' : 'Добавление';
$this->title .= ' реквизитов для ' . $model->paymentName;
?>
<div class="content__section">
    <div class="info">
        <span class="title-block"><?= Html::encode($this->title) ?></span>
        <div class="info__block">

            <div class="form">

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'payment_detail')->textInput(['maxlength' => true, 'placeholder' => $model->paymentTip]) ?>

                <div class="info__title">Внимание!</div>
                <p class="info__text">
                    Нужно ввести пароль от личного кабинета Вплюсе, а не от платежной системы!
                </p>
                <?= $form->field($model, 'currentPassword')->passwordInput(['maxlength' => true]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>