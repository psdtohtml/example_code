<?php

use app\enums\OrderType;
use app\enums\Status;
use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\modules\admin\models\search\Orders */
/* @var $modelCode */
/* @var $autoCompleteLimit */

$this->title = 'Bookings';
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="booking-index">

        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?=
            yii\helpers\Html::button( \Yii::t('app', 'Create New Order'), [
                'value' => Url::to(['/admin/orders/create-new-order']),
                'title' => 'Create New Order',
                'class' => 'showModalButton btn btn-success',
                'id'    => 'submit-create-order'
            ]);
            ?>
        </p>

        <div class="right">
            <?= Html::a(\Yii::t('app', 'Clear Filters'), ['manage'],
                ['class' => 'btn left-space btn-primary', 'id' => 'reset-search-button']) ?>
        </div>

        <?php
        $gridColumns = [
            [
                'label' => 'Ref',
                'attribute' => 'id',
                'format' => 'raw',
                'value'=>function ($model) {
                    return
                        Html::a(Html::encode(OrderType::getTypeById(OrderType::BOOKING).'-'.$model['id']),['manage-booking-detail', 'id' => $model['id']]).' / '.
                        Html::a(Html::encode(OrderType::getTypeById(OrderType::ORDER).'-'.$model['order_id']),['/admin/orders/manage-order-detail', 'id' => $model['order_id']]);
                },
                'contentOptions' => ['class' => 'word-break'],
                'filter' => kartik\typeahead\Typeahead::widget([
                    'model' => $model,
                    'attribute' => 'id',
                    'value' => $model->id,
                    'dataset' => [
                        [
                            'display' => Yii::$app->params['common.autoсomplete.display.key'],
                            'remote' => [
                                'url' => \yii\helpers\Url::to([
                                    'suggestion',
                                    'code' => $modelCode,
                                    'field' => 'id',
                                    'value' => '%value',
                                ]),
                                'wildcard' => '%25value',
                            ],
                            'limit' => $autoCompleteLimit,
                        ],
                    ],
                    'pluginOptions' =>
                        [
                            'highlight' => true,
                            'minLength' => 3,
                        ],
                ]),
            ],
            [
                'label' => $model->getAttributeLabel('customer'),
                'attribute' => 'customer',
                'format' => 'raw',
                'value'=>function ($model) {
                    if(isset($model['customer'])) {
                        return
                            Html::a(Html::encode($model['customer']), ['/admin/customer/manage-customer', 'id' => $model['customerId']]);
                    }else{
                        return '-';
                    }
                },
                'contentOptions' => ['class' => 'word-break'],
                'filter' => kartik\typeahead\Typeahead::widget([
                    'model' => $model,
                    'attribute' => 'customer',
                    'value' => $model->customer,
                    'dataset' => [
                        [
                            'display' => Yii::$app->params['common.autoсomplete.display.key'],
                            'remote' => [
                                'url' => \yii\helpers\Url::to([
                                    'suggestion',
                                    'code' => $modelCode,
                                    'field' => 'customer',
                                    'value' => '%value',
                                ]),
                                'wildcard' => '%25value',
                            ],
                            'limit' => $autoCompleteLimit,
                        ],
                    ],
                    'pluginOptions' =>
                        [
                            'highlight' => true,
                            'minLength' => 3,
                        ],
                ]),
            ],
            [
                'label' => $model->getAttributeLabel('product') .' / '. $model->getAttributeLabel('priceTicket'),
                'attribute' => 'product',
                'format' => 'raw',
                'value'=>function ($model) {
                    if(isset($model['product'])){
                        return
                            Html::a(Html::encode($model['product']),["/admin/tour/manage?Tour%5Bname%5D={$model['product']}",])
                            .' / '.
                            yii\helpers\BaseStringHelper::truncate($model['priceTicket'], 80) . ' ' . yii\helpers\BaseStringHelper::truncate(\app\enums\Currency::getCurrencySymbol((string)\app\enums\Currency::getTitleByType($model['currency'])), 80);
                    }else{
                        return '-';
                    }

                },
                'contentOptions' => ['class' => 'word-break'],
                'filter' => kartik\typeahead\Typeahead::widget([
                    'model' => $model,
                    'attribute' => 'product',
                    'value' => $model->product,
                    'dataset' => [
                        [
                            'display' => Yii::$app->params['common.autoсomplete.display.key'],
                            'remote' => [
                                'url' => \yii\helpers\Url::to([
                                    'suggestion',
                                    'code' => $modelCode,
                                    'field' => 'product',
                                    'value' => '%value',
                                ]),
                                'wildcard' => '%25value',
                            ],
                            'limit' => $autoCompleteLimit,
                        ],
                    ],
                    'pluginOptions' =>
                        [
                            'highlight' => true,
                            'minLength' => 3,
                        ],
                ]),
            ],
            [
                'label' => $model->getAttributeLabel('status').' / '. $model->getAttributeLabel('valid_from'),
                'attribute' => 'status',
                'format' => 'raw',
                'value'=>function ($model) {
                    return
                        yii\helpers\BaseStringHelper::truncate(Status::getStatusById($model['status']), 80).' / '.
                        Yii::$app->formatter->asDatetime(yii\helpers\BaseStringHelper::truncate($model['valid_from'], 80), 'dd-M-Y HH:mm');
                },
                'contentOptions' => ['class' => 'word-break'],
                'filter' => kartik\typeahead\Typeahead::widget([
                    'model' => $model,
                    'attribute' => 'status',
                    'value' => $model->status,
                    'dataset' => [
                        [
                            'display' => Yii::$app->params['common.autoсomplete.display.key'],
                            'remote' => [
                                'url' => \yii\helpers\Url::to([
                                    'suggestion',
                                    'code' => $modelCode,
                                    'field' => 'status',
                                    'value' => '%value',
                                ]),
                                'wildcard' => '%25value',
                            ],
                            'limit' => $autoCompleteLimit,
                        ],
                    ],
                    'pluginOptions' =>
                        [
                            'highlight' => true,
                            'minLength' => 3,
                        ],
                ]),
            ],
            'size',
        ];
        ?>

        <?= \kartik\export\ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'showConfirmAlert' => false,
            'target'=>ExportMenu::TARGET_SELF,
            'columns' => [
                'id',
                'order_id',
                'customer',
                'product',
                'status',
                'valid_from',
                'valid_to',
                'size',
            ],
            'exportConfig' => [
                ExportMenu::FORMAT_TEXT    => false,
                ExportMenu::FORMAT_PDF     => false,
                ExportMenu::FORMAT_EXCEL   => false,
                ExportMenu::FORMAT_EXCEL_X => false,
                ExportMenu::FORMAT_HTML    => false,
            ],
        ])
        ;?>

        <?= \kartik\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'summary'=>'',
            'filterModel' => $model,
            'columns' => $gridColumns,
            'pjax' => true,
        ]); ?>

    </div>

<?php $this->registerJs('
$(function () {


function test(){
$.pjax.reload({container: "#w3-pjax"});
};

//refresh
setTimeout(test, 5000);

});




', yii\web\View::POS_END, 'myOwn'); ?>