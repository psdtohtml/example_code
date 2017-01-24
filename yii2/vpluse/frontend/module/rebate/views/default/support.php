<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\rebate\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Техподдержка';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content__section">
    <div class="info">
        <span class="title-block"><?= Html::encode($this->title) ?></span>
        <div class="info__block">
            <div class="info__title">Задайте свой вопрос</div>
            <div class="row">
                <div class="col-lg-5">
                    <?php $form = ActiveForm::begin(['id' => 'contact-form'], 'post'); ?>

                    <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>

                    <?= $form->field($model, 'email') ?>

                    <?= $form->field($model, 'subject') ?>

                    <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                        'template' => '
                            <div class="row">
                                <div class="col-lg-6 captcha">{image}</div>
                                <div class="col-lg-6">{input}</div>
                            </div>',
                        'captchaAction'=>'/site/captcha'
                    ]) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
