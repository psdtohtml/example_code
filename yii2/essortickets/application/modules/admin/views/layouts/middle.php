<?php
/**
 * @var \yii\web\View $this
 * @var string $content
 * @version 1.0
 * @copyright (c) 2016-2017 KFOSOFT Team <kfosoftware@gmail.com>
 */

use \app\widgets\growl\GrowlNotifier;
use \app\assets\AppAsset;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;

AppAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= \Yii::$app->language ?>">
<head>
    <meta charset="<?= \Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= yii\helpers\Html::csrfMetaTags() ?>
    <title><?= yii\helpers\Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?= GrowlNotifier::widget([
    'pluginOptions' => [
        'placement' => [
            'from' => 'bottom',
            'align' => 'right'
        ],
    ],

]); ?>
<?php $this->beginBody() ?>
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
        'items' => [
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
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container middle-layout">
        <div id="page-loading">
            <div class="show-loading"></div>
            <div class="img-load"></div>
        </div>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; ITS <?= date('Y') ?></p>

        <p class="pull-right"><?= 'Powered by <a href="http://www.kfosoftware.net/" rel="external">KFOSOFT</a>'; ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
