<?php
use frontend\assets\OutputAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model frontend\models\rebate\OutputForm */

$this->title = 'Вывести средства';
OutputAsset::register($this);

?>
<div class="content__section">
    <div class="info">
        <span class="title-block"><?= Html::encode($this->title) ?></span>
        <div class="info__block">

            <div class="form">

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'pay_system')->dropDownList($model->paySystems, ['id'=>'pay_system']) ?>
                <div id="not_have_payment_detail" class="alert alert-danger">
                    Прежде чем выводить средства на эту платежную систему, вам нужно указать ваш кошелек.
                    Сделать это можно в разделе
                    <a href="<?= Url::to(['/rebate/balance/payment-details']) ?>">«Платежные реквизиты»</a>
                </div>

                <?= $form->field($model, 'payment_detail')->textInput(['id'=>'payment_detail','readonly' => true]) ?>

                <?= $form->field($model, 'credit')->textInput() ?>


                <div class="form-group">
                    <?= Html::submitButton('Отправить заявку', ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>