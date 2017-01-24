<?php

use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @var $this yii\web\View
 * @var $modelCustomer \app\models\Customer
 * @var $modelBooking \app\modules\admin\models\search\Booking
 * @var $dataProvider
 * @var $modelMessages \app\models\Messages
 * @var $dataProviderMessages \app\services\Messages
 */

$this->title = $modelCustomer->name . ' ' . $modelCustomer->last_name;
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
    <?=
    yii\helpers\Html::button( \Yii::t('app', 'Update Customer'), [
        'value' => Url::to(['update-customer', 'id' => $modelCustomer->id]),
        'title' => 'Update Customer',
        'class' => 'showModalButton btn btn-success',
        'id'    => 'submit-create-tour'
    ]);
    ?>
</p>

<p>
    <?=
    yii\helpers\Html::button( \Yii::t('app', 'Send Message'), [
        'value' => Url::to(['send-custom-message', 'id' => $modelCustomer->id]),
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
            <?= $this->render('partials/_booking_details', [
                'model'           => $modelCustomer,
                'totalSpentMoney' => $totalSpentMoney,
            ]); ?>
        </div>
        <div class="form-group well">
            <?= $this->render('partials/_address_details', [
                'model'         => $modelCustomer,
            ]); ?>
        </div>
    </div>
</div>

<?= $this->render('partials/_grid_booking', [
    'dataProvider'         => $dataProvider,
    'modelBooking'        => $modelBooking,
]); ?>

<?= $this->render('partials/_grid_messages', [
    'dataProviderMessages' => $dataProviderMessages,
    'modelMessages'        => $modelMessages,
]); ?>

