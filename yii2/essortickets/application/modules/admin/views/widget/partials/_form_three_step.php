<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var array $events*/

?>
        <?= app\widgets\fullcalendar\Fullcalendar::widget([
            'events'        => $events,
            'header'        => [
                'center' => 'title',
                'left'   => 'next, today',
                'right'  => 'agendaWeek',
            ],
            'clientOptions'  => [
                'locale'     => 'en',
                'timeFormat' => 'H:mm',
            ]
        ]);
        ?>
