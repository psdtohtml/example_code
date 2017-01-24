<?php
/**
 * @var $modelTour \app\models\data\Tour
 * @var $modelOrders \app\models\data\Orders
 * @var $modelEvent \app\models\data\Events
 * @var $modelBooking \app\models\data\Booking
 * @var string $customer \app\services\Customer
 */
?>

<hr>
<div class="common">
    <p>
    Product: <?= $modelTour->name; ?><br/>
    Customer: <?= $customer; ?><br/>
    Admin Email: <?= Yii::$app->params['admin.email']; ?><br/>
    Size: <?= $modelOrders->size; ?><br/>
    Valid: <?= $modelEvent->start_date . ' / ' . $modelEvent->start_time; ?><br/>
    Reference: <?= \app\enums\OrderType::getTypeById(\app\enums\OrderType::BOOKING) . '-' . $modelBooking->id; ?><br/>
    </p>
    <hr>
    <?= $modelTour->confirmation_mail; ?>
    <hr>
</div>