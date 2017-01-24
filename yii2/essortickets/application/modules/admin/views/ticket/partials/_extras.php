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
    <h3><?= yii\helpers\Html::encode(\Yii::t('app', 'Extras')) ?></h3>

    <?php Pjax::begin([
        'id' => 'pjax-grid-extras'
    ]);?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            'name',
            'price',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'delete' => function ($url) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"></span>',
                            $url,
                            [
                                'data' => [
                                    'confirm' => \Yii::t('app', 'Delete extras?'),
                                    'data-method' => 'GET',
                                ],
                            ]
                        );
                    },
                    'update' => function ($url, $model) {
                        /** @var string $newUrl */
                        $newUrl =Url::to(['update-extras', 'id' => $model->id, 'modelsType'=>\app\enums\Models::EXTRAS]);
                        return Html::a(
                            "<span title='Update Extra' id='grid-update-extras' class='showModalButton glyphicon glyphicon-pencil' value='$newUrl'></span>",
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
                        return Url::to(['delete', 'id' => $key, 'modelsType'=>\app\enums\Models::EXTRAS]);
                    }

                    return '';
                },

            ],
        ],
    ]);
    ?>

    <?=
    yii\helpers\Html::button( \Yii::t('app', 'Create New Extras'), [
        'value' => Url::to(['create-extras', 'id' => $dataProvider->query->where['t.tour_id']]),
        'title' => 'Create New Extras',
        'class' => 'showModalButton btn btn-success',
        'id'    => 'submit-create-extras'
    ]);
    ?>
</div>

<?php Pjax::end();?>
