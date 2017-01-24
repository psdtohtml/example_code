<?php
use yii\helpers\Url;
?>
<div class="sidebar">
    <ul class="sidebar__nav">
        <li class="sidebar__row">
            <a class="sidebar__item sidebar__item-balance" href="/partner">
                <span class="sidebar__title">Промо материалы</span>
            </a>
        </li>
        <li class="sidebar__row">
            <a class="sidebar__item sidebar__item-balance" href="/partner/customers">
                <span class="sidebar__title">Клиенты</span>
            </a>
        </li>
        <li class="sidebar__row">
            <a class="sidebar__item sidebar__item-balance js-drop" href="#?">
                <span class="sidebar__title">Начисления</span>
            </a>
            <ul class="dropdown">
                <li class="dropdown__row">
                    <a class="dropdown__item" href="<?= Url::to(['/partner/history']) ?>">История начислений</a>
                </li>
                <li class="dropdown__row">
                    <a class="dropdown__item" href="<?= Url::to(['/pay/output']) ?>">Вывести средства</a>
                </li>
                <li class="dropdown__row">
                    <a class="dropdown__item" href="<?= Url::to(['/pay/payment-details']) ?>"">Платежные реквизиты</a>
                </li>
            </ul>
        </li>
    </ul>
    <a class="show-menu" href="#?">
        <span class="show-menu__ico"></span>
        <span class="show-menu__title">свернуть</span>
    </a>
</div>