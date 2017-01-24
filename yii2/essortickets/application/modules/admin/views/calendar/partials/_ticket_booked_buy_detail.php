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
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use \yii\helpers\Html;
use yii\widgets\Pjax;

?>

<h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Confirmed Tickets')) ?></h4>

<?= yii\helpers\Html::encode('Confirmed' . ' ' . $model->size_use) ?>

<?= yii\helpers\Html::encode('Pending' . ' ' . $model->size_pending) ?>

<?php Pjax::begin(); ?>

<?php
$gridColumns = [
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
        'label' =>$modelTicketBookedBuy->getAttributeLabel('bookingId'),
        'attribute' => 'bookingId',
        'value' => function ($modelTicketBookedBuy) {
            if(isset($modelTicketBookedBuy['bookingId'])) {
                return  OrderType::labelById(OrderType::BOOKING) . '-' . $modelTicketBookedBuy['bookingId'];
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
        'label' =>$modelTicketBookedBuy->getAttributeLabel('customer'),
        'attribute' => 'customer',
        'value' => 'customer',
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
];?>

<?= \kartik\export\ExportMenu::widget([
    'dataProvider' => $dataProviderTicketBookedBuy,
    'showConfirmAlert' => false,
    'target'=>ExportMenu::TARGET_SELF,
    'columns' => [
        'id',
        'bookingId',
        'orderStatus',
        'customer',
        'tier',
        'size',
    ],
    'exportConfig' => [
        ExportMenu::FORMAT_TEXT    => false,
        ExportMenu::FORMAT_PDF     => false,
        ExportMenu::FORMAT_EXCEL   => false,
        ExportMenu::FORMAT_EXCEL_X => false,
        ExportMenu::FORMAT_HTML    => false,
    ],
])
;?>

<?= \kartik\grid\GridView::widget([
    'dataProvider' => $dataProviderTicketBookedBuy,
    'summary'=>'',
    'columns' => $gridColumns
]); ?>

<?php Pjax::end(); ?>
