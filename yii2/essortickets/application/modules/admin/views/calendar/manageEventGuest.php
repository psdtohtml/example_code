<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var array $events */
/* @var $modelTicketBookedBuy app\modules\admin\models\search\TicketBookedBuy */

$this->title = $model->title;
?>
<div class="events-view">
    <h1><?= Html::encode($this->title) . ' | ' . 'Manifest' ?></h1>

    <div class="form-part-right sidebar-465">
        <div class="right">
            <?= Html::a(\Yii::t('app', 'Back to calendar'), ['manage-calendar'],
                ['class' => 'btn left-space btn-primary', 'id' => 'back-to-calendar']) ?>
        </div>
        <div class="form-group well">
            <?= $this->render('partials/_ticket_booked_buy_detail_guest', [
                'model' => $model,
                'products' => Yii::$app->tour->getTourNames(),
                'users'    => Yii::$app->user_component->getUsers(),
                'modelTicketBookedBuy' => $modelTicketBookedBuy,
                'dataProviderTicketBookedBuy' => $modelTicketBookedBuy->search(Yii::$app->request->get(), '', $model->id),
            ]); ?>
        </div>
    </div>
</div>