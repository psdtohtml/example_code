<?php

use app\enums\OrderType;
use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model \app\models\Orders */
/* @var $dataProvider \app\modules\admin\models\search\Orders */
/* @var $username */

$this->title = 'Order' . ' ' . OrderType::getTypeById(OrderType::ORDER). '-'. $model->id;
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
if($model->status == \app\enums\Status::PROVISIONAL || $model->status == \app\enums\Status::PENDING || $model->status == \app\enums\Status::EDITING){
    echo \app\widgets\countdown\Countdown::widget([
        'datetime' => date('Y-m-d H:i:s', $model->created_at + \app\enums\Status::getCountdownById($model->status)),
        'format' => '%H : %M : %S',
        'events' => [
            'finish' => "function(){
            $.ajax({
                    type: 'POST',
                    url: '" . \yii\helpers\Url::to(['countdown', 'id' => $model->id]) ."',
                })
            }",
        ],
    ]);
}
?>
    <p>
        <?php
        if($model->status != \app\enums\Status::CANCELED){
            echo Html::a(\Yii::t('app', 'Cancel Order'), ['cancel-order', 'id' => $model->id],
                ['class' => 'btn btn-success', 'id' => 'reset-search-button']);
        }
        ?>
    </p>
    <p>
        <?php
        if($model->status == \app\enums\Status::PENDING){
            echo Html::a(\Yii::t('app', 'Change Status To Confirm'), ['confirm-order', 'id' => $model->id],
                ['class' => 'btn btn-success', 'id' => 'reset-search-button']);
        }
        ?>
    </p>

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="form-part-right sidebar-465">
        <div class="form-group">
            <div class="form-group well">
                <?= $this->render('partials/_order_details', [
                    'dataProvider'  => $dataProvider,
                    'model'         => $model,
                    'username'      => $username,
                    'customer'      => $customer,
                ]); ?>
            </div>
            <div class="form-group well">
                <?= $this->render('partials/_add_booking', [
                    'dataProvider'         => $dataProvider,
                    'model'                => $model,
                    'modelBooking'         => $modelBooking,
                    'dataProviderBooking'  => $dataProviderBooking,
                    'username'             => $username,
                ]); ?>
            </div>
            <div class="form-group well">
                <?= $this->render('partials/_customer_information', [
                    'model'                => $model,
                    'customers'            => $customers,
                ]); ?>
            </div>
            <?php if ($model->confirm == 0 && $model->customer_id !== Null) : ?>
                <div class="form-group well">
                    <?= $this->render('partials/_confirm_details', [
                        'model'                => $model,
                    ]); ?>
                </div>
            <?php endif;?>
        </div>
    </div>

    </div>

<?php $this->registerJs("

");?>