<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Channels';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="channel-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?=
        yii\helpers\Html::button( \Yii::t('app', 'Create Channel'), [
            'value' => Url::to(['create']),
            'title' => 'Create Channel',
            'class' => 'showModalButton btn btn-success',
            'id'    => 'submit-create-channel'
        ]);
        ?>
    </p>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary'=>'',
        'columns' => [
            [
                'label' => $model->getAttributeLabel('name'),
                'attribute' => 'name',
                'value' => function ($model) {
                    return yii\helpers\BaseStringHelper::truncate($model['name'], 80);
                }
            ],
            [
                'label' => $model->getAttributeLabel('product_id'),
                'attribute' => 'product_id',
                'value' => function ($model) {
                    return yii\helpers\BaseStringHelper::truncate(Yii::$app->tour->getProductNameProductId($model['product_id']), 80);
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{web_integration} {update} {delete}',
                'buttons' => [
                    'delete' => function ($url) {
                        /** @var integer $channelId */
                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"></span>',
                            $url,
                            [
                                'data' => [
                                    'confirm' => \Yii::t('app', 'Delete Channel?'),
                                    'data-method' => 'GET',
                                ],
                            ]
                        );
                    },
                    'update' => function ($url, $model) {
                        /** @var string $newUrl */
                        $newUrl =Url::to(['update', 'id' => $model['id']]);
                        /** @var integer $channelId */
                        return Html::a(
                            "<span title='Update Tour' id='grid-update-tour' class='showModalButton glyphicon glyphicon-pencil' value='$newUrl'></span>",
                            $url,
                            [
                                'data' => [
                                    'data-method' => 'GET',
                                ],
                            ]
                        );
                    },
                    'web_integration' => function ($url) {
                        /** @var integer $channelId */
                        return Html::a(
                            Yii::t('app','Web Integration'),
                            $url,
                            [
                                'data' => [
                                    'data-method' => 'GET',
                                ],
                                'class' => 'btn btn-primary btn-xs',
                            ]
                        );
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'delete') {
                        return Url::to(['delete', 'id' => $key,]);
                    } elseif ($action === 'web_integration') {
                        return Url::to(['manage-channel', 'id' => $key,]);
                    }

                    return '';
                },

            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
