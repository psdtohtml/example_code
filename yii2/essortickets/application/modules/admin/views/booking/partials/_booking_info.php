<?php
/**
 * @var yii\web\View $this
 * @var app\models\Booking $model
 * @var float $total_price app\services\Orders
 * @var float $currency app\services\Orders
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use app\enums\OrderType;
use app\enums\Status;
use \yii\helpers\Html;

?>
<h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Booking information')) ?></h4>
<?php if(isset($customer)) : echo Html::label(\Yii::t('app', 'Customer')) . ': '. Html::a(Html::encode($customer->name . " " . $customer->last_name),['/admin/customer/manage-customer', 'id' => $customer->id]); else : echo Html::label(\Yii::t('app', 'Customer')) . ': '. '-'; endif; ?> <br/>
<?= Html::label(\Yii::t('app', 'Reference')) . ': '. OrderType::getTypeById(OrderType::BOOKING). '-'. $model->id ?> <br/>
<?= Html::label(\Yii::t('app', 'Order')) . ': ' . Html::a(OrderType::getTypeById(OrderType::ORDER). '-'. $model->order_id,['/admin/orders/manage-order-detail', 'id'=> $model->order_id],['id' => 'order']) ?><br/>
<?= Html::activeLabel($model, 'valid_from') . ": {$model->valid_from}" ?> <br/>
<?= Html::activeLabel($model, 'status') . ": " . Status::getStatusById($model->status) ?> <br/>
<?php if(!is_null($model->booking_price)) : echo Html::activeLabel($model, 'booking_price') . ": " . $model->booking_price . \app\enums\Currency::getCurrencySymbol((string)\app\enums\Currency::getTitleByType(Yii::$app->tour->getCurrencyByOrderId($model->order_id))); else : echo Html::activeLabel($model, 'booking_price') . ': '. '-'; endif; ?> <br/>
<?= Html::activeLabel($model, 'size') . ': '. $peopleSize ?> <br/>
