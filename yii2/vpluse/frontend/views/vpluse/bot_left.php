<?php
use frontend\widgets\SectionMenuWidget;
?>
<div class="sidebar">
    <ul class="sidebar__nav">
        <li class="sidebar__row">
            <a class="sidebar__item sidebar__item-support" href="/rebate/default/support">
                <span class="sidebar__title">Техподдержка</span>
            </a>
        </li>
        <li class="sidebar__row">
            <a class="sidebar__item sidebar__item-profile" href="/rebate/profile">
                <span class="sidebar__title">Мой профиль</span>
            </a>
        </li>
        <li class="sidebar__row">
            <a class="sidebar__item sidebar__item-balance" href="#?">
                <span class="sidebar__title">Баланс</span>
            </a>
        </li>
    </ul>
    <a class="show-menu" href="#?">
        <span class="show-menu__ico"></span>
        <span class="show-menu__title">свернуть</span>
    </a>
</div>