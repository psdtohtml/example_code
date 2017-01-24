<?php
use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use backend\widgets\CustomGridWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\rebate\SearchOutput */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заявки на вывод партнерских средств';
?>

<div class="box box-warning">
    <div class="box-header">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>
    <div class="box-body">
        <?= CustomGridWidget::widget([
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' =>
                [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute'=>'status',
                        'format'=>'text',
                        'content'=>function($data){
                            switch ($data->status) {
                                case 0 : $color = 'primary'; break;
                                case 1: $color = 'success'; break;
                                default: $color = 'warning'; break;
                            }
                            return '<span class="label label-' . $color . '">' . $data->statusName . '</span>';
                        },
                        'filter'=>$searchModel->getStatusArrayNames()
                    ],
                    'fullName',
                    'login',
                    'amount',
                    'balancePartner',
                    'payment_detail',
                    [
                        'attribute'=>'created_at',
                        'format' => ['date', 'php: d.m.Y'],
                        'filter' => DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'created_at',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'dd.mm.yyyy'
                            ]
                        ])
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update-status} {delete}',
                        'buttons' => [
                            'update-status' => function($url, $data){
                                if($data->status == 1) {
                                    return false;
                                }
                                return Html::a('<span class="glyphicon glyphicon-new-window"></span>', $url, [
                                    'title' => 'Вывести средства']);
                            },
                            'delete' => function($url){
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                    'title' => 'Удалить',
                                    'data' => [
                                        'confirm' => 'Вы действительно хотите удалить заявку?',
                                        'method' => 'post',
                                    ],
                                ]);
                            },
                        ]
                    ]
                ]
        ]) ?>
    </div>
</div>
