<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Мой профиль';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content__section">
    <div class="info">
        <span class="title-block"><?= Html::encode($this->title) ?></span>
        <div class="info__block">
            <p>
                <?= Html::a('Редактировать профиль', ['update'], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Изменить пароль', ['password-change'], ['class' => 'btn btn-primary']) ?>
            </p>
            <br>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute'=>'avatar',
                        'value'=>'/uploads/avatar/' . $model->getAvatar(),
                        'format' => ['image',['width'=>'100']],
                        'label' => 'Аватар',
                    ],
                    'username',
                    'email:email',
                    'first_name',
                    'last_name',
                    'subscriptionStatus',
                ],
            ]) ?>
        </div>
    </div>
</div>

