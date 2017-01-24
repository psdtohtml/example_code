<?php

use yii\helpers\Html;
use backend\widgets\CustomGridWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\rebate\SearchCompany */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Партнерский баланс';
?>
<div class="box box-warning">
    <div class="box-header">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>
    <div class="box-body">
        <?= CustomGridWidget::widget([
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' =>[
                ['class' => 'yii\grid\SerialColumn'],

                'fullName',
                'username',
                'balance_partner',
            ],
        ]); ?>
    </div>
</div>
