<?php

use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var array $events*/

$this->title = 'Calendar';
if(!Yii::$app->user->isGuest){
    $this->params['breadcrumbs'][] = $this->title;
}
?>
    <div class="events-index">

        <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(['id' => 'downloadSourceCode', 'action' =>'/admin/calendar/manage-calendar']); ?>
        <?= Html::DropDownList('id', null, $tours) ?>
        <?= Html::submitButton('Submit') ?>
        <?php ActiveForm::end(); ?>


        <?= app\widgets\fullcalendar\Fullcalendar::widget([
            'events'        => $events,
            'clientOptions'  => [
                'locale'     => 'en',
                'timeFormat' => 'H:mm',
            ]
        ]);
        ?>

        <?php
        Modal::begin([
            'header' => '<h4>Calendar</h4>',
            'id'     => 'modal-event',
            'size'   => 'modal-lg',
        ]);

        echo "<div id='modal-event-content'></div>";

        Modal::end();
        ?>

    </div>

<?php
if(!Yii::$app->user->isGuest){
    $this->registerJs("
$(function(){

    $(document).on('click', '.fc-day',function(){
        var date = $(this).attr('data-date');

        $.get('/admin/calendar/create', {'date':btoa(date)}, function(data){
            $('#modal-event').modal('show')
            .find('#modal-event-content')
            .html(data);
        });
    });

});
");
}

