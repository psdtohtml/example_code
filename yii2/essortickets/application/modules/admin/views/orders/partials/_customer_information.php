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
use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use \yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

?>
    <h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Customer information')) ?></h4>


<?php Pjax::begin([
    'id'=>'pjax-forms',
]); ?>

<?php $form = ActiveForm::begin([
    'id' => 'modal-form-tour',
    'action' => Url::to(['update-customer', 'id' =>$model->id])
]); ?>

    <!-- Customer ID -->
<?= $form->field($model, 'customer_id')->widget(Select2::classname(),
    [
        'name' => 'kv-type-01',
        'data' => $customers,
        'options' => [
            'placeholder' => 'Select customer ...',
            'options' => []
        ],

    ])->label(false)
?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <p>
        <?= yii\helpers\Html::button( \Yii::t('app', 'Create Customer'), [
            'value' => Url::to(['/admin/customer/create', 'id' => $model->id]),
            'title' => 'Create Customer',
            'class' => 'showModalFormButton btn btn-primary',
            'id'    => 'submit-add-booking'
        ]);
        ?>
    </p>

<?php ActiveForm::end(); ?>

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