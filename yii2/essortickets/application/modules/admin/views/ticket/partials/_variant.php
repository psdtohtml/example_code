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
    <h3><?= yii\helpers\Html::encode(\Yii::t('app', 'Variant')) ?></h3>

    <?php Pjax::begin([
        'id' => 'pjax-grid-variant'
    ]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            'name',

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
                                    'confirm' => \Yii::t('app', 'Delete variant?'),
                                    'data-method' => 'GET',
                                ],
                            ]
                        );
                    },
                    'update' => function ($url, $model, $tourId) {
                        /** @var string $newUrl */
                        $newUrl =Url::to(['update-variant', 'id' => $model->id, 'modelsType'=>\app\enums\Models::VARIANT, 'id' => $tourId]);
                        /** @var integer $tourId */
                        return Html::a(
                            "<span title='Update Variant' id='grid-update-variant' class='showModalButton glyphicon glyphicon-pencil' value='$newUrl'></span>",
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
                        return Url::to(['delete', 'id' => $key, 'modelsType'=>\app\enums\Models::VARIANT]);
                    }

                    return '';
                },

            ],
        ],
    ]); ?>
    <?=
    yii\helpers\Html::button( \Yii::t('app', 'Create New Variant'), [
        'value' => Url::to(['create-variant', 'id' => $tourId]),
        'title' => 'Create New Variant',
        'class' => 'showModalButton btn btn-success',
        'id'    => 'submit-create-variant'
    ]);
    ?>
</div>

<?php Pjax::end();?>
