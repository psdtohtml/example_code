<?php
$avatar = Yii::$app->params['avatarURL'] . 'no_photo.jpg';
if(Yii::$app->user->identity->avatar && file_exists(Yii::getAlias("@avatar") . Yii::$app->user->identity->avatar)) {
    $avatar = Yii::$app->params['avatarURL'] . Yii::$app->user->identity->avatar;
}
$service = isset($_SESSION['service']) ? $_SESSION['service'] : null;
?>
<header class="header">
    <div class="block__container">
        <div class="logo">
            <a class="logo__wrapper" href="/">
                <img class="logo__img" src="/img/logo.png" alt="logo">
                <img class="logo__mob" src="img/logo_mob.png" alt="logo">
            </a>
        </div>
        <div class="authorization">
            <a href="/site/logout" class="logout btn_main">Выйти</a>
        </div>
        <div class="user">
            <div class="user__info user__info_height">
                <div class="user__photo">
                    <img src="<?= $avatar  ?>" alt="user_photo">
                </div>
                <div class="user__description">
                    <span class="user__title"><?= Yii::$app->user->identity->first_name ?> <?= Yii::$app->user->identity->last_name ?></span>
                    <span class="user__subtitle"><?= Yii::$app->user->identity->username ?></span>
                </div>
            </div>
            <div class="user__info user__info_margin">
                <div class="user__ico">
                    <img src="/img/balance.png" alt="balance_photo">
                </div>
                <div class="user__description">
                    <span class="user__title"><?= $service != 'partner' ? 'Баланс' : 'Партнерский баланс' ?></span>
                    <span class="user__subtitle"><?= $service != 'partner' ? Yii::$app->user->identity->balance : Yii::$app->user->identity->balance_partner ?>$</span>
                </div>
            </div>
        </div>
    </div>
</header>