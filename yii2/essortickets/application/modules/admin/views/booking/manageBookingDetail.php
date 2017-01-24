<?php

use app\enums\OrderType;
use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\search\Booking */
/* @var $dataProvider \app\modules\admin\models\search\Booking */
/* @var app\modules\admin\models\search\TicketBookedBuy $dataProviderTicket */
/* @var app\modules\admin\models\search\TicketBookedBuy $modelTicket */
/* @var app\modules\admin\models\search\Question $modelQuestion */
/* @var app\modules\admin\models\search\Question $dataProviderQuestion */
/* @var app\services\TicketBookedBuy $peopleSize */
/* @var app\services\Tour $product */
$this->title = 'Booking' . ' ' . OrderType::getTypeById(OrderType::BOOKING). '-'. $model->id . '   ' . $product;
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
    <?php
    if($model->status != \app\enums\Status::CANCELED){
        echo Html::a(\Yii::t('app', 'Cancel Booking'), ['cancel-booking', 'id' => $model->id],
            ['class' => 'btn btn-success', 'id' => 'reset-search-button']);
    }
    ?>
    <?php
    if($model->status === \app\enums\Status::CONFIRMED){
    echo Html::a('<i class="glyphicon glyphicon-download-alt"></i> Download Ticket', ['report', 'id' => $model->id],
        [
        'class'=>'btn btn-danger',
        'target'=>'_blank',
        'data-toggle'=>'tooltip',
        'title'=>'Will download the generated PDF file'
        ]);
    }
    ?>
    <?php
    if($model->status === \app\enums\Status::CONFIRMED){
    echo Html::a(\Yii::t('app', 'Send Confirmation'), ['send-confirmation', 'id' => $model->id],
    [
    'class'=>'btn btn-primary',
    'title'=>'Will send confirm message to customer'
    ]);
    }
    ?>
    <?=
    yii\helpers\Html::button( \Yii::t('app', 'Send Message'), [
        'value' => Url::to(['send-custom-message', 'id' => $customerId, 'bookingId' => $model->id]),
        'title' => 'Send Message To Customer',
        'class' => 'showModalButton btn btn-success',
        'id'    => 'submit-sent-message'
    ]);
    ?>
</p>

<h1><?= Html::encode($this->title) ?></h1>

<div class="form-part-right sidebar-465">
    <div class="form-group">
        <div class="form-group well">
            <?= $this->render('partials/_booking_info', [
                'dataProvider'  => $dataProvider,
                'model'         => $model,
                'peopleSize'    => $peopleSize,
                'customer'      => $customer,
            ]); ?>
        </div>
        <?php if(isset($modelMessage)):?>
            <div class="form-group well">
                <?= $this->render('partials/_message', [
                    'model'         => $modelMessage,
                ]); ?>
            </div>
        <?php endif; ?>
        <div class="form-group well">
            <?=
            $this->render('partials/_ticket_info', [
                'modelTicket'        => $modelTicket,
                'dataProviderTicket' => $dataProviderTicket,
                'peopleSize'         => $peopleSize,
            ]); ?>
        </div>
        <div class="form-group well">
            <?= $this->render('partials/_extra_info', [
                'modelTicket'        => $modelTicket,
                'dataProviderTicket' => $dataProviderTicket,
            ]); ?>
        </div>
        <div class="form-group well">
            <?= $this->render('partials/_answers', [
                'modelQuestion'        => $modelQuestion,
                'dataProviderQuestion' => $dataProviderQuestion,
            ]); ?>
        </div>
    </div>
</div>

</div>