<?php
/**
 * @var yii\web\View $this
 * @var $model app\models\Events
 * @var $dataProviderTicket
 * @var $modelTicket
 * @var $peopleSize
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use app\enums\OrderType;
use app\enums\Status;
use kartik\grid\GridView;
use \yii\helpers\Html;
use yii\widgets\Pjax;

?>

<h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Confirmed Tickets')) ?></h4>

<?= yii\helpers\Html::encode('Confirmed' . ' ' . $model->size_use) ?>

<?= yii\helpers\Html::encode('Pending' . ' ' . $model->size_pending) ?>

<?php Pjax::begin(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProviderTicketBookedBuy,
    'summary'=>'',
    'columns' => [
        [
            'label' =>$modelTicketBookedBuy->getAttributeLabel('id'),
            'attribute' => 'id',
            'value' => function ($modelTicketBookedBuy) {
                if(isset($modelTicketBookedBuy['id'])) {
                    return  OrderType::labelById(OrderType::TICKET) . '-' . $modelTicketBookedBuy['id'];
                }else{
                    return '-';
                }}
        ],
        [
            'label' =>$modelTicketBookedBuy->getAttributeLabel('orderStatus'),
            'attribute' => 'orderStatus',
            'format' => 'raw',
            'value' => function ($modelTicketBookedBuy) {
                return
                    yii\helpers\BaseStringHelper::truncate(Status::getStatusById($modelTicketBookedBuy['orderStatus']), 80);
            },
        ],
        [
            'label' =>$modelTicketBookedBuy->getAttributeLabel('tier'),
            'attribute' => 'tier',
            'value' => 'tier',
        ],
        [
            'label' =>$modelTicketBookedBuy->getAttributeLabel('size'),
            'attribute' => 'size',
            'value' => 'size',
        ],
    ],
]); ?>

<?php Pjax::end(); ?>
