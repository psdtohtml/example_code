<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ChangePasswordForm */

$this->title = Yii::t('app', 'Изменение пароля');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'TITLE_PROFILE'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content__section">
    <div class="info">
        <span class="title-block"><?= Html::encode($this->title) ?></span>
        <div class="info__block">

            <div class="user-form">

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'currentPassword')->passwordInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'newPassword')->passwordInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'newPasswordRepeat')->passwordInput(['maxlength' => true]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>