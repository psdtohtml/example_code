<?php
/* @var $this yii\web\View */
/* @var $listDataProvider object*/

use yii\widgets\ListView;

$this->title = 'Новости';

echo ListView::widget([
    'dataProvider' => $listDataProvider,
    'itemView' => '_list_news',
    'summary' => 'Показано {count} из {totalCount}',

]);
