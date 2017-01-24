<?php
use yii\helpers\Url;

/** @var $this \yii\web\View */
/** @var $link string */
/** @var $title string */

?>
<h4><?= $title ?></h4>
<p><?= $text ?></p>
<p>
    <a href="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['site/news', 'id' => $news_id]); ?>">Перейти на сайт</a></p>
<br>
<br>
<a href="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['rebate/profile/view', 'id' => $user_id]); ?>">Отписаться от рассылки</a>