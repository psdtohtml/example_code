<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Coupons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?=
        yii\helpers\Html::button( \Yii::t('app', 'Create Coupon'), [
            'value' => Url::to(['create']),
            'title'=> 'Create Coupon',
            'class' => 'showModalButton btn btn-success',
            'id'    => 'submit-create-coupon'
        ]);
        ?>
    </p>
<?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' =>  $model,
        'columns' => [

            [
                'label' => $model->getAttributeLabel('name'),
                'attribute' => 'name',
                'value' => function ($model) {
                    return yii\helpers\BaseStringHelper::truncate($model['name'], 80);
                },
                'contentOptions' => ['class' => 'word-break'],
                'filter' => kartik\typeahead\Typeahead::widget([
                    'model' => $model,
                    'attribute' => 'name',
                    'value' => $model->name,
                    'dataset' => [
                        [
                            'display' => Yii::$app->params['common.autoсomplete.display.key'],
                            'remote' => [
                                'url' => \yii\helpers\Url::to([
                                    'suggestion',
                                    'code' => $modelCode,
                                    'field' => 'name',
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
                'label' => $model->getAttributeLabel('code'),
                'attribute' => 'code',
                'value' => function ($model) {
                    return yii\helpers\BaseStringHelper::truncate($model['code'], 80);
                },
                'contentOptions' => ['class' => 'word-break'],
                'filter' => kartik\typeahead\Typeahead::widget([
                    'model' => $model,
                    'attribute' => 'code',
                    'value' => $model->code,
                    'dataset' => [
                        [
                            'display' => Yii::$app->params['common.autoсomplete.display.key'],
                            'remote' => [
                                'url' => \yii\helpers\Url::to([
                                    'suggestion',
                                    'code' => $modelCode,
                                    'field' => 'code',
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
                'label' => $model->getAttributeLabel('ticket_id'),
                'attribute' => 'ticket_id',
                'value' => function ($model) {
                    return yii\helpers\BaseStringHelper::truncate($model['ticket_id'], 80);
                },
                'contentOptions' => ['class' => 'word-break'],
                'filter' => kartik\typeahead\Typeahead::widget([
                    'model' => $model,
                    'attribute' => 'ticket_id',
                    'value' => $model->ticket_id,
                    'dataset' => [
                        [
                            'display' => Yii::$app->params['common.autoсomplete.display.key'],
                            'remote' => [
                                'url' => \yii\helpers\Url::to([
                                    'suggestion',
                                    'code' => $modelCode,
                                    'field' => 'ticket_id',
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
                            'minLength' => 1,
                        ],
                ]),
            ],
            [
                'label' => $model->getAttributeLabel('used'),
                'attribute' => 'limit',
                'value' => function ($model) {
                    return yii\helpers\BaseStringHelper::truncate($model['used'], 80);
                },
                'contentOptions' => ['class' => 'word-break'],
                'filter' => kartik\typeahead\Typeahead::widget([
                    'model' => $model,
                    'attribute' => 'used',
                    'value' => $model->used,
                    'dataset' => [
                        [
                            'display' => Yii::$app->params['common.autoсomplete.display.key'],
                            'remote' => [
                                'url' => \yii\helpers\Url::to([
                                    'suggestion',
                                    'code' => $modelCode,
                                    'field' => 'used',
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
                            'minLength' => 1,
                        ],
                ]),
            ],
            [
                'label' => $model->getAttributeLabel('limit'),
                'attribute' => 'limit',
                'value' => function ($model) {
                    return yii\helpers\BaseStringHelper::truncate($model['limit'], 80);
                },
                'contentOptions' => ['class' => 'word-break'],
                'filter' => kartik\typeahead\Typeahead::widget([
                    'model' => $model,
                    'attribute' => 'limit',
                    'value' => $model->limit,
                    'dataset' => [
                        [
                            'display' => Yii::$app->params['common.autoсomplete.display.key'],
                            'remote' => [
                                'url' => \yii\helpers\Url::to([
                                    'suggestion',
                                    'code' => $modelCode,
                                    'field' => 'limit',
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
                            'minLength' => 1,
                        ],
                ]),
            ],
            [
                'label'     => $model->getAttributeLabel('status'),
                'attribute' => 'status',
                'value'     => 'status',
                'format'    => 'boolean',
                'filter'    => app\enums\YesNo::listData()
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'delete' => function ($url) {
                        /** @var integer $ticketId */
                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"></span>',
                            $url,
                            [
                                'data' => [
                                    'confirm' => \Yii::t('app', 'Delete tiers?'),
                                    'data-method' => 'GET',
                                ],
                            ]
                        );
                    },
                    'update' => function ($url, $model) {
                        /** @var string $newUrl */
                        $newUrl =Url::to(['update', 'id' => $model['id']]);
                        /** @var integer $tourId */
                        return Html::a(
                            "<span title='Update Coupon' id='grid-update-coupon' class='showModalButton glyphicon glyphicon-pencil' value='$newUrl'></span>",
                            $url,
                            [
                                'data' => [
                                    'data-method' => 'GET',
                                ],
                            ]
                        );
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'delete') {
                        return Url::to(['delete', 'id' => $key,]);
                    }

                    return '';
                },

            ],
        ],
    ]); ?>

<?php Pjax::end(); ?>

</div>
