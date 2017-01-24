<?php
use backend\widgets\CustomGridWidget;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\rebate\HistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Журнал движения средств';
?>
<div class="box box-warning">
    <div class="box-header">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>
    <div class="box-body">
        <?= CustomGridWidget::widget([
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                //'orientationName',
                [
                    'attribute'=>'orientation',
                    'format'=>'text',
                    'content'=>function($data){
                        switch ($data->orientation) {
                            case 0 : $color = 'success'; break;
                            case 1: $color = 'danger'; break;
                            case 2 : $color = 'primary'; break;
                            default: $color = 'warning'; break;
                        }
                        return '<span class="label label-' . $color . '">' . $data->orientationName . '</span>';
                    },
                    'filter'=>$searchModel->orientationArrayNames()
                ],
                'fullName',
                'login',
                'credit',
                'from_where',
                'note',
                'adminLogin',
                [
                    'attribute'=>'operation_date',
                    'format' => ['date', 'php: d.m.Y H:i:s'],
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'operation_date',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd.mm.yyyy'
                        ]
                    ])
                ],
            ],
        ]); ?>
    </div>
</div>
