<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Taxes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tax-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?=
        yii\helpers\Html::button( \Yii::t('app', 'Create Tax'), [
            'value' => Url::to(['create-tax']),
            'title' => 'Create Tax',
            'class' => 'showModalTaxButton btn btn-success',
            'id'    => 'submit-create-tax'
        ]);
        ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary'=>'',
        'columns' => [
            'name',
            'amount',
            [
                'label'     => $model->getAttributeLabel('cumulative'),
                'attribute' => 'cumulative',
                'value'     => 'cumulative',
                'format'    => 'boolean',
                'filter'    => app\enums\YesNo::listData()
            ],
            [
                'label'     => $model->getAttributeLabel('included'),
                'attribute' => 'included',
                'value'     => 'included',
                'format'    => 'boolean',
                'filter'    => app\enums\YesNo::listData()
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}{delete}',
                'buttons' => [
                    'delete' => function ($url) {
                        /** @var integer $taxId */
                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"></span>',
                            $url,
                            [
                                'data' => [
                                    'confirm' => \Yii::t('app', 'Delete tax?'),
                                    'data-method' => 'GET',
                                ],
                            ]
                        );
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'delete') {
                        return Url::to(['delete', 'id' => $key,]);
                    }elseif ($action === 'update') {
                        return Url::to(['update-tax', 'id' => $key]);
                    }

                    return '';
                },

            ],
        ],
    ]); ?>
</div>

<!--//-------------------------------------------------------------->
<?php
yii\bootstrap\Modal::begin([
    'header' => '<h4>'.\Yii::t('app', 'Add Tax').'</h4>',
    'footer' => Html::tag('span', \Yii::t('app', 'Close'), $options = [
        'class' => 'btn btn-primary button-modal-css',
        'id' => 'exit-form-modal',
    ]),
    'headerOptions' => [
        'id' => 'modalHeader'
    ],
    'id' => 'modal-form',
    'size' => 'modal-lg',
    'closeButton' =>FALSE,
    'clientOptions' => [
        'backdrop' => 'static',
        'keyboard' => FALSE,
    ]
]);
echo "<div id='modalContent'></div>";
yii\bootstrap\Modal::end();
?>
<!--//-------------------------------------------------------------->


<?php $this->registerJs('
// call back Url
var callBackUrl = document.URL;
    
$(function(){
    $(document).on("click", ".showModalTaxButton", function(){
        if ($("#modal-form").data("bs.modal").isShown) {
            $("#modal-form").find("#modalContent")
                .load($(this).attr("value"));
        } else {
            $("#modal-form").modal("show")
                .find("#modalContent")
                .load($(this).attr("value"));
        }
    });
});

//close modal window
$(document).on("click", "#exit-form-modal",function () {
    $("#modal-form").modal("hide");
    window.location = callBackUrl;
});
//close modal window
$(document).on("click", "#close",function () {
    $("#modal-form").modal("hide");
    window.location = callBackUrl;
});

');?>
