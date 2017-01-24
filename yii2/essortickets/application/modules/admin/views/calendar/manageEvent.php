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
/** @var $model app\models\Events */

$this->title = $model->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="events-view">
    <h1><?= Html::encode($this->title) . ' | ' . 'Manifest' ?></h1>

    <div class="right">
        <?= Html::a(\Yii::t('app', 'Broadcast Message'), ['broadcast-message', 'id' => $model->id],
            ['class' => 'btn left-space btn-primary', 'id' => 'broadcast-message-button']) ?>
    </div>

    <div class="form-part-right sidebar-465">
        <div class="form-group">
            <div class="form-group well">
                <?= $this->render('partials/_ticket_booked_buy_detail', [
                    'model' => $model,
                    'products' => Yii::$app->tour->getTourNames(),
                    'users'    => Yii::$app->user_component->getUsers(),
                    'modelTicketBookedBuy' => $modelTicketBookedBuy,
                    'dataProviderTicketBookedBuy' => $dataProviderTicketBookedBuy,
                ]); ?>
            </div>
            <div class="form-group well">
                <?= $this->render('partials/_form_update', [
                    'model' => $model,
                    'products' => Yii::$app->tour->getTourNames(),
                    'users'    => Yii::$app->user_component->getUsers(),
                ]); ?>
            </div>
            </div>
        </div>
    </div>

</div>