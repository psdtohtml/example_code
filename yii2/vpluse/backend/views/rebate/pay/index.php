<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Платежные системы';
?>
<div class="box box-warning">
    <div class="box-header">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>
    <div class="box-body">
        <p>
            <?= Html::button('Добавить платежную систему', ['value' => Url::to('/rebate/pay/create'), 'class' => 'btn btn-success modal-button']) ?>
        </p>
        <?= GridView::widget([
            'pager' => [
                'firstPageLabel' => 'Первая страница',
                'lastPageLabel'  => 'Последняя страница'
            ],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'summary' => 'Показано {count} из {totalCount}',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'name',
                'code',
                'tip',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function($url){
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', 'javascript:void(0)', [
                                'value' => $url,
                                'class' => 'modal-button']);
                        },
                        'delete' => function($url){
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'class' => '',
                                'data' => [
                                    'confirm' => 'Вы действительно хотите удалить систему?',
                                    'method' => 'post',
                                ],
                            ]);
                        }
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
