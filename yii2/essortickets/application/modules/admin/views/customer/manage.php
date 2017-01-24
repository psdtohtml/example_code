<?php

use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\modules\admin\models\search\Customer */
/* @var $modelCode */
/* @var $autoCompleteLimit */


$this->title = 'Customer Relationship Manager';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="right">
        <?= Html::a(\Yii::t('app', 'Clear Filters'), ['manage'],
            ['class' => 'btn left-space btn-primary', 'id' => 'reset-search-button']) ?>
    </div>

    <?php Pjax::begin(); ?>
    
    <?php
    $gridColumns = [
        [
            'label' => $model->getAttributeLabel('fullName'),
            'attribute' => 'fullName',
            'format' => 'raw',
            'value'=>function ($model) {
                return Html::a(Html::encode($model['fullName']),['manage-customer', 'id' => $model['id']]);
            },
            'contentOptions' => ['class' => 'word-break'],
            'filter' => kartik\typeahead\Typeahead::widget([
                'model' => $model,
                'attribute' => 'fullName',
                'value' => $model->fullName,
                'dataset' => [
                    [
                        'display' => Yii::$app->params['common.autoсomplete.display.key'],
                        'remote' => [
                            'url' => \yii\helpers\Url::to([
                                'suggestion',
                                'code' => $modelCode,
                                'field' => 'fullName',
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
            'label' => $model->getAttributeLabel('email'),
            'attribute' => 'email',
            'value' => function ($model) {
                return yii\helpers\BaseStringHelper::truncate($model['email'], 80);
            },
            'contentOptions' => ['class' => 'word-break'],
            'filter' => kartik\typeahead\Typeahead::widget([
                'model' => $model,
                'attribute' => 'email',
                'value' => $model->email,
                'dataset' => [
                    [
                        'display' => Yii::$app->params['common.autoсomplete.display.key'],
                        'remote' => [
                            'url' => \yii\helpers\Url::to([
                                'suggestion',
                                'code' => $modelCode,
                                'field' => 'email',
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
            'label'     => $model->getAttributeLabel('subscribed'),
            'attribute' => 'subscribed',
            'value'     => 'subscribed',
            'format'    => 'boolean',
            'filter'    => app\enums\YesNo::listData()
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
        ],
    ];?>

    <?= \kartik\export\ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'showConfirmAlert' => false,
        'target'=>ExportMenu::TARGET_SELF,
        'columns' => [
            'id',
            'title',
            'name',
            'last_name',
            'company',
            'email',
            'country',
            'city',
            'street_address',
            'zip',
            'phone',
            'ip',
            'subscribed',
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
        'columns' => $gridColumns
    ]); ?>

    <?php Pjax::end();?>

</div>
