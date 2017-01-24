<?php
/**
 * @var $modelTour \app\models\data\Tour
 * @var $modelOrders \app\models\data\Orders
 * @var $modelEvent \app\models\data\Events
 * @var $modelBooking \app\models\data\Booking
 * @var $modelTicket \app\models\data\Ticket
 * @var $modelTicketBookedBuy \app\models\data\TicketBookedBuy
 * @var integer $totalPrice
 * @var string $customer \app\services\Customer
 * @var string $linkInfo
 */
?>

<hr>
<div class="block1">
    <?php
    // TODO add this to img src
    //yii\helpers\BaseUrl::base(true) . '/uploads/' . $modelTour->logo
    // ?>
    Logo: <p><img src="https://encrypted-tbn2.gstatic.com/images?q=tbn:ANd9GcSgbA3jT0BfgQR136K0aZMWOaL9BrZiElQ5xZ5IUDltwXXWveq7xEZyaIw" alt="Письма мастера дзен"></p><br/>
    Product: <?= $modelTour->name;?><br/>
    Meeting point: <?=$modelTour->meeting_point;?><br/>
    Emergencies phone: <?=$modelTour->e_phone;?><br/>
    Tier type: <?=$modelTicket->name;?><br/>
    Price paid: <?=$totalPrice;?><br/>
    Number of pax: <?= 1;?><br/>
    Date time:<?= $modelEvent->start_date . ' / ' . $modelEvent->start_time;?><br/>
    Link info: <?= $linkInfo;?><br/>
    Ref BK: <?= \app\enums\OrderType::getTypeById(\app\enums\OrderType::BOOKING) . '-' . $modelBooking->id;?><br/>
    Ref TK: <?=\app\enums\OrderType::getTypeById(\app\enums\OrderType::TICKET) . '-' . $modelTicketBookedBuy->id;?><br/>
    Customer: <?= $customer; ?><br/>
</div>
<hr>