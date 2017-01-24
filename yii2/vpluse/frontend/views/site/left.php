<?php
use yii\helpers\Url;

?>
<div class="sidebar">
    <ul class="sidebar__nav">
        <li class="sidebar__row">
            <a class="sidebar__item sidebar__item-support" href="<?= Url::to(['/site/news']) ?>">
                <span class="sidebar__title">Новости</span>
            </a>
        </li>
        <li class="sidebar__row">
            <a class="sidebar__item sidebar__item-profile" href="<?= Url::to(['/site/signup']) ?>">
                <span class="sidebar__title">Регистрация</span>
            </a>
        </li>
    </ul>
    <a class="show-menu" href="#?">
        <span class="show-menu__ico"></span>
        <span class="show-menu__title">свернуть</span>
    </a>
</div>