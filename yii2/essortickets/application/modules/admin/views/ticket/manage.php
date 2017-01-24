<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\search\Ticket */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var int $modelCode Model code for suggestions */
/* @var $autoCompleteLimit */

$this->title = 'Tickets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="right">
        <?= Html::a(\Yii::t('app', 'Clear Filters'), ['manage'],
            ['class' => 'btn left-space btn-primary', 'id' => 'reset-search-button']) ?>
    </div>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $model,
        'columns' => [
            [
                'label' => $model->getAttributeLabel('id'),
                'attribute' => 'id',
                'value' => 'id',
            ],
            [
                'label' => $model->getAttributeLabel('tour_id'),
                'attribute' => 'tour_id',
                'value' => function ($model) {
                    return yii\helpers\BaseStringHelper::truncate($model['tour_id'], 80);
                },
                'contentOptions' => ['class' => 'word-break'],
                'filter' => kartik\typeahead\Typeahead::widget([
                    'model' => $model,
                    'attribute' => 'tour_id',
                    'value' => $model->tour_id,
                    'dataset' => [
                        [
                            'display' => Yii::$app->params['common.autoсomplete.display.key'],
                            'remote' => [
                                'url' => \yii\helpers\Url::to([
                                    'suggestion',
                                    'code' => $modelCode,
                                    'field' => 'tour_id',
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
                'label' => $model->getAttributeLabel('description'),
                'attribute' => 'description',
                'value' => function ($model) {
                    return yii\helpers\BaseStringHelper::truncate($model['description'], 80);
                },
                'contentOptions' => ['class' => 'word-break'],
                'filter' => kartik\typeahead\Typeahead::widget([
                    'model' => $model,
                    'attribute' => 'description',
                    'value' => $model->description,
                    'dataset' => [
                        [
                            'display' => Yii::$app->params['common.autoсomplete.display.key'],
                            'remote' => [
                                'url' => \yii\helpers\Url::to([
                                    'suggestion',
                                    'code' => $modelCode,
                                    'field' => 'description',
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
                'label' => $model->getAttributeLabel('price'),
                'attribute' => 'price',
                'value' => function ($model) {
                    return yii\helpers\BaseStringHelper::truncate($model['price'], 80);
                },
                'contentOptions' => ['class' => 'word-break'],
                'filter' => kartik\typeahead\Typeahead::widget([
                    'model' => $model,
                    'attribute' => 'price',
                    'value' => $model->price,
                    'dataset' => [
                        [
                            'display' => Yii::$app->params['common.autoсomplete.display.key'],
                            'remote' => [
                                'url' => \yii\helpers\Url::to([
                                    'suggestion',
                                    'code' => $modelCode,
                                    'field' => 'price',
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
                            'minLength' => 2,
                        ],
                ]),
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
                                    'confirm' => \Yii::t('app', 'Delete tour?'),
                                    'data-method' => 'GET',
                                ],
                            ]
                        );
                    },
                    'update' => function ($url) {
                        /** @var integer $tourId */
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
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
                    } elseif ($action === 'update') {
                        return Url::to(['manage-ticket', 'id' => $model['tour_id'],]);
                    }

                    return '';
                },

            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
