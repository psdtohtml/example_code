<?php
/**
 * @var $dataProvider yii\data\ActiveDataProvider
 *
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

?>
<div class="well">
    <h3><?= yii\helpers\Html::encode(\Yii::t('app', 'Question')) ?></h3>

    <?php Pjax::begin([
        'id' => 'pjax-grid-tiers'
    ]);?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            'question',

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
                        $newUrl =Url::to(['update-question', 'id' => $model->id, 'modelsType'=>\app\enums\Models::QUESTION]);
                        /** @var integer $tourId */
                        return Html::a(
                            "<span title='Update Question' id='grid-update-question' class='showModalButton glyphicon glyphicon-pencil' value='$newUrl'></span>",
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
                        return Url::to(['delete', 'id' => $key, 'modelsType'=>\app\enums\Models::QUESTION]);
                    }

                    return '';
                },

            ],
        ],
    ]); ?>

    <?=
    yii\helpers\Html::button( \Yii::t('app', 'Create New Question'), [
        'value' => Url::to(['create-question', 'id' => $dataProvider->query->where['t.tour_id']]),
        'title' => 'Create New Question',
        'class' => 'showModalButton btn btn-success',
        'id'    => 'submit-create-tiers'
    ]);
    ?>

    <?php Pjax::end();?>

</div>
