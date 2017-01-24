<?php

use app\enums\Status;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelBooking app\modules\admin\models\search\Booking */

?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'summary'=>'',
    'columns' => [
        [
            'label' => $modelBooking->getAttributeLabel('id'),
            'format' => 'raw',
            'value'=>function ($modelBooking) {
                return Html::a(\app\enums\OrderType::getTypeById(\app\enums\OrderType::BOOKING) . '-' . Html::encode($modelBooking['id']),['/admin/booking/manage-booking-detail', 'id' => $modelBooking['id']]);
            },
        ],
        'customer',
        'product',
        'valid_from',
        [
            'label' => $modelBooking->getAttributeLabel('status'),
            'attribute' => 'status',
            'format' => 'raw',
            'value'=>function ($modelBooking) {
                return yii\helpers\BaseStringHelper::truncate(Status::getStatusById($modelBooking['status']), 80);
            }
        ]
    ],
]); ?>
