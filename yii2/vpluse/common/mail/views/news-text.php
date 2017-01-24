<?php
use yii\helpers\Url;

/** @var $this \yii\web\View */
/** @var $link string */
/** @var $title string */

?>
<?= $title ?>\n
<?= $text ?>\n
Перейти на сайт:
<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['site/news', 'id' => $news_id]); ?>\n
    \n
    \n
    \n
Отписаться от рассылки:
<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['rebate/profile/view', 'id' => $user_id]); ?>