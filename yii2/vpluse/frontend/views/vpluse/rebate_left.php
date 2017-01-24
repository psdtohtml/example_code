<?php
use yii\helpers\Url;
use frontend\widgets\SectionMenuWidget;
?>
<div class="sidebar">
    <ul class="sidebar__nav">
        <?= SectionMenuWidget::widget(); ?>
        <li class="sidebar__row">
            <a class="sidebar__item sidebar__item-support" href="<?= Url::to(['/rebate/default/support']) ?>">
                <span class="sidebar__title">Техподдержка</span>
            </a>
        </li>
        <li class="sidebar__row">
            <a class="sidebar__item sidebar__item-profile" href="<?= Url::to(['/rebate/profile']) ?>">
                <span class="sidebar__title">Мой профиль</span>
            </a>
        </li>
        <li class="sidebar__row">
            <a class="sidebar__item sidebar__item-balance js-drop" href="#?">
                <span class="sidebar__title">Баланс</span>
            </a>
            <ul class="dropdown">
                <li class="dropdown__row">
                    <a class="dropdown__item" href="<?= Url::to(['/rebate/balance']) ?>">История операций</a>
                </li>
                <li class="dropdown__row">
                    <a class="dropdown__item" href="<?= Url::to(['/pay/output']) ?>">Вывести средства</a>
                </li>
                <li class="dropdown__row">
                    <a class="dropdown__item" href="<?= Url::to(['/pay/payment-details']) ?>">Платежные реквизиты</a>
                </li>
            </ul>
        </li>
    </ul>
    <a class="show-menu" href="#?">
        <span class="show-menu__ico"></span>
        <span class="show-menu__title">свернуть</span>
    </a>
</div>