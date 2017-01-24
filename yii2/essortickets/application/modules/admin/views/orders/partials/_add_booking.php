<?php
/**
 * @var yii\web\View $this
 * @var $username
 * @var app\models\Orders $model
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use app\enums\OrderType;
use app\enums\Status;
use kartik\grid\GridView;
use \yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

?>
    <h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Bookings')) ?></h4>

<?php Pjax::begin(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProviderBooking,
    'summary'=>'',
    'columns' => [
        [
            'label' => 'Ref',
            'attribute' => 'id',
            'format' => 'raw',
            'value'=>function ($modelBooking) {
                return
                    Html::a(Html::encode(OrderType::getTypeById(OrderType::BOOKING).'-'.$modelBooking['id']),['/admin/booking/manage-booking-detail', 'id' => $modelBooking['id']]);
            },
            'contentOptions' => ['class' => 'word-break'],
        ],
        [
            'label' => 'Date booking',
            'attribute' => 'valid_from',
            'format' => ['datetime'],
            'value'=> 'valid_from',
        ],
        [
            'label' => 'Product',
            'attribute' => 'product',
            'value'=> $modelBooking['product'],
        ],
    ]
]); ?>
    <p>
        <?php if(!isset($modelBooking)){
            echo yii\helpers\Html::button( \Yii::t('app', 'Add booking'), [
                'value' => Url::to(['/admin/booking/create-booking', 'id' => $model->id]).'?product=',
                'title' => 'Add Booking',
                'class' => 'showModalFormButton btn btn-primary',
                'id'    => 'submit-add-booking'
            ]);
        }
        ?>
    </p>

<?php Pjax::end(); ?>

    <!--//-------------------------------------------------------------->
<?php
yii\bootstrap\Modal::begin([
    'header' => '<h4>'.\Yii::t('app', 'Add Booking').'</h4>',
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
    $(document).on("click", ".showModalFormButton", function(){
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