<?php
/**
 * @var $dataProvider yii\data\ActiveDataProvider
 *
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

?>
<div class="well">
    <h3><?= yii\helpers\Html::encode(\Yii::t('app', 'Seasons')) ?></h3>

    <?php Pjax::begin([
        'id' => 'pjax-grid-season'
    ]);?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            'name',
            'start_on',
            'ends_on',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{detail} {duplicate} {update} {delete}',
                'buttons' => [
                    'delete' => function ($url) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"></span>',
                            $url,
                            [
                                'data' => [
                                    'confirm' => \Yii::t('app', 'Delete season?'),
                                    'data-method' => 'GET',
                                ],
                            ]
                        );
                    },
                    'detail' => function ($url, $model) {
                        /** @var string $newUrl */
                        $newUrl =Url::to(['manage-detail', 'id' => $model->id]);
                        return Html::a(
                            "<span title='Detail Season' id='grid-detail-season' class='showModalButton glyphicon glyphicon-calendar' value='$newUrl'></span>",
                            $url,
                            [
                                'data' => [
                                    'data-method' => 'GET',
                                ],
                            ]
                        );
                    },
                    'duplicate' => function ($url) {
                        return Html::a(
                            "<span title='Duplicate Season' id='grid-duplicate-season' class='glyphicon glyphicon-duplicate'></span>",
                            $url,
                            [
                                'data' => [
                                    'confirm' => \Yii::t('app', 'Duplicate season?'),
                                    'data-method' => 'GET',
                                ],
                            ]
                        );
                    },
                    'update' => function ($url, $model) {
                        /** @var string $newUrl */
                        $newUrl =Url::to(['update', 'id' => $model->id, 'type' => \app\enums\Availability::SEASON_TYPE, 'modelsType'=>\app\enums\Models::AVAILABILITY]);
                        return Html::a(
                            "<span title='Update Season' id='grid-update-season' class='showModalButton glyphicon glyphicon-pencil' value='$newUrl'></span>",
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
                        return Url::to(['delete', 'id' => $key, 'modelsType'=>\app\enums\Models::AVAILABILITY]);
                    }elseif ($action === 'duplicate') {
                        return Url::to(['duplicate', 'id' => $model->id, 'type' => \app\enums\Availability::SEASON_TYPE, 'modelsType'=>\app\enums\Models::AVAILABILITY]);
                    }

                    return '';
                },

            ],
        ],
    ]);
    ?>
    <?=
    yii\helpers\Html::button( \Yii::t('app', 'Create New Season'), [
        'value' => Url::to(['create', 'type' => \app\enums\Availability::SEASON_TYPE]),
        'title' => 'Create New Season',
        'class' => 'showModalButton btn btn-success',
        'id'    => 'submit-create-extras'
    ]);
    ?>
</div>

<?php Pjax::end();?>
