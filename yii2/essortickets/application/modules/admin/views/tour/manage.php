<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\search\Tour */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var int $modelCode Model code for suggestions */
/* @var $autoCompleteLimit */

$this->title = 'Tours';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tour-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?=
        yii\helpers\Html::button( \Yii::t('app', 'Create Tour'), [
            'value' => Url::to(['create']),
            'title' => 'Create Tour',
            'class' => 'showModalButton btn btn-success',
            'id'    => 'submit-create-tour'
        ]);
        ?>
    </p>
    <div class="right">
        <?= Html::a(\Yii::t('app', 'Clear Filters'), ['manage'],
            ['class' => 'btn left-space btn-primary', 'id' => 'reset-search-button']) ?>
    </div>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $model,
        'columns' => [
            [
                'label' => $model->getAttributeLabel('id'),
                'attribute' => 'id',
                'value' => 'id',
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
                'label' => $model->getAttributeLabel('recurring'),
                'attribute' => 'recurring',
                'value' => function ($model) {
                    return yii\helpers\BaseStringHelper::truncate(\app\enums\EventRecurring::getTitleByType($model['recurring']), 80);
                },
                'contentOptions' => ['class' => 'word-break'],
                'filter' => kartik\typeahead\Typeahead::widget([
                    'model' => $model,
                    'attribute' => 'recurring',
                    'value' => $model->recurring,
                    'dataset' => [
                        [
                            'display' => Yii::$app->params['common.autoсomplete.display.key'],
                            'remote' => [
                                'url' => \yii\helpers\Url::to([
                                    'suggestion',
                                    'code' => $modelCode,
                                    'field' => 'recurring',
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
                'label' => $model->getAttributeLabel('currency'),
                'attribute' => 'currency',
                'value' => function ($model) {
                    return \app\enums\Currency::getCurrencySymbol((string)\app\enums\Currency::getTitleByType($model['currency']));
                },
                'contentOptions' => ['class' => 'word-break'],
                'filter' => kartik\typeahead\Typeahead::widget([
                    'model' => $model,
                    'attribute' => 'currency',
                    'value' => $model->currency,
                    'dataset' => [
                        [
                            'display' => Yii::$app->params['common.autoсomplete.display.key'],
                            'remote' => [
                                'url' => \yii\helpers\Url::to([
                                    'suggestion',
                                    'code' => $modelCode,
                                    'field' => 'currency',
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
                'label' => $model->getAttributeLabel('ticket_available'),
                'attribute' => 'ticket_available',
                'value' => function ($model) {
                    return yii\helpers\BaseStringHelper::truncate($model['ticket_available'], 80);
                },
                'contentOptions' => ['class' => 'word-break'],
                'filter' => kartik\typeahead\Typeahead::widget([
                    'model' => $model,
                    'attribute' => 'ticket_available',
                    'value' => $model->ticket_available,
                    'dataset' => [
                        [
                            'display' => Yii::$app->params['common.autoсomplete.display.key'],
                            'remote' => [
                                'url' => \yii\helpers\Url::to([
                                    'suggestion',
                                    'code' => $modelCode,
                                    'field' => 'ticket_available',
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
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {variants} {stats} {ticket} {instances} {delete}',
                'buttons' => [
                    'delete' => function ($url) {
                        /** @var integer $tourId */
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
                    'view' => function ($url) {
                        /** @var integer $tourId */
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            $url,
                            [
                                'data' => [
                                    'data-method' => 'GET',
                                ],
                            ]
                        );
                    },
                    'stats' => function ($url) {
                        /** @var integer $tourId */
                        return Html::a(
                            '<span class="glyphicon glyphicon-signal"></span>',
                            $url,
                            [
                                'data' => [
                                    'data-method' => 'GET',
                                ],
                            ]
                        );
                    },
                    'ticket' => function ($url) {
                        /** @var integer $tourId */
                        return Html::a(
                            '<span class="glyphicon glyphicon-bookmark"></span>',
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
                    }elseif ($action === 'view') {
                        return Url::to(['view', 'id' => $key,]);
                    } elseif ($action === 'stats') {
                        return Url::to(['stats', 'id' => $key,]);
                    } elseif ($action === 'ticket') {
                        return Url::to(['/admin/ticket/manage-ticket', 'id' => $key,]);
                    }

                    return '';
                },

            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?></div>
