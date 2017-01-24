<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?php echo \app\widgets\growl\GrowlNotifier::widget([
    'pluginOptions' => [
        'placement' => [
            'from' => 'bottom',
            'align' => 'right'
        ],
    ],

]); ?>

<!--//-------------------------------------------------------------->
<?php
yii\bootstrap\Modal::begin([
    'footer' => Html::tag('span', \Yii::t('app', 'Close'), $options = [
            'class' => 'btn btn-primary button-modal-css',
            'id' => 'exit-modal',
        ]),
    'headerOptions' => ['id' => 'modalHeader'],
    'id' => 'modal',
    'size' => 'modal-lg',
    //keeps from closing modal with esc key or by clicking out of the modal.
    // user must click cancel or X to close
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
]);
echo "<div id='modalContent'></div>";
yii\bootstrap\Modal::end();
?>
<!--//-------------------------------------------------------------->

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Essor Tickets',
        'brandUrl' => '/admin/tour/manage',
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' =>
            !Yii::$app->user->isGuest ?([
            ['label' => 'Channel', 'url' => ['/admin/channel/manage']],
            ['label' => 'Calendar', 'url' => ['/admin/calendar/manage-calendar']],
            ['label' => 'Options', 'url' => ['/admin/options/manage']],
            ['label' => 'Tour', 'url' => ['/admin/tour/manage']],
            ['label' => 'Ticket', 'url' => ['/admin/ticket/manage']],
            ['label' => 'Availability', 'url' => ['/admin/availability/manage']],
            ['label' => 'Customers', 'url' => ['/admin/customer/manage']],
            ['label' => 'Coupon', 'url' => ['/admin/coupon/manage']],
            ['label' => 'Booking', 'url' => ['/admin/booking/manage']],
            //['label' => 'Orders', 'url' => ['/admin/orders/manage']],
            ['label' => 'Admin', 'url' => ['/user/admin/index']],

            '<li>'
            . Html::beginForm(['/user/security/logout '], 'post', ['class' => 'navbar-form'])
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link']
            )
            . Html::endForm()
            . '</li>'
            ]) : ([
            Yii::$app->user->isGuest ? (
            ['label' => 'Login', 'url' => ['/']]
            ) : (
                '<li>'
                . Html::beginForm(['/user/security/logout '], 'post', ['class' => 'navbar-form'])
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link']
                )
                . Html::endForm()
                . '</li>'
            )
        ])
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
