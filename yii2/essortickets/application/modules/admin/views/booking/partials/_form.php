<?php

use kartik\widgets\DateTimePicker;
use kartik\widgets\RangeInput;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Orders */
/* @var $form yii\widgets\ActiveForm */
/* @var $products app\services\Tour */
?>

    <div class="booking-form">

        <?php $form = ActiveForm::begin([
            'options' => [
                'data-pjax' => '1'
            ],
            'id' => 'addBooking',
        ]); ?>
        <!-- ------------------------------------------------------------------------------------------------------- -->
        <div class="form-group well">
            <!-- Product name -->
            <?= $form->field($model, 'product')->dropDownList($products,
                [
                    'onchange'=>'
            $.pjax.reload({
            url: "'.Url::to(['create-booking']).'?id='.$order_id.'&product="+$(this).val(),
            container: "#pjax-booking-form",
            timeout: 0,
            });
        ',

                    'id' => 'form-product-drop',
                    'class'=>'form-control',
                    'prompt' => 'Select Product...'
                ]) ?>
        </div>
        <!-- ------------------------------------------------------------------------------------------------------- -->
        <?php  Pjax::begin(['id'=>'pjax-booking-form','enablePushState'=>false]);     ?>
        <!-- ------------------------------------------------------------------------------------------------------- -->
            <div class="form-group well">
                <!-- Tiers -->
                <?= $form->field($modelTickedBookedBuy, 'ticket_id')->dropDownList($tiers,
                    [
                        'onchange'=>'
            $.pjax.reload({
            url: "'.Url::to(['create-booking']).'?id='.$order_id.'&product='.$tour_id.'&tier="+$(this).val(),
            container: "#pjax-booking-form2",
            timeout: 0,
            });
        ',
                        'id' => 'form-tier-drop',
                        'class'=>'form-control',
                        'prompt' => 'Select Tiers...'
                    ])->label('Tier') ?>
            </div>
        <!-- ------------------------------------------------------------------------------------------------------- -->
        <?php  Pjax::begin(['id'=>'pjax-booking-form2','enablePushState'=>false]);     ?>
        <!-- ------------------------------------------------------------------------------------------------------- -->
            <div class="form-group well">
                <!-- Ticket date -->
                <?= $form->field($modelTickedBookedBuy, 'start_date')->dropDownList($dates,
                    [
                        'onchange'=>'
            $.pjax.reload({
            url: "'.Url::to(['create-booking']).'?id='.$order_id.'&product='.$tour_id.'&tier='.$tier.'&start="+btoa($(this).val()),
            container: "#pjax-booking-form3",
            timeout: 0,
            });
        ',
                        'id' => 'form-start-date-drop',
                        'class'=>'form-control',
                        'prompt' => 'Select Start Date...'
                    ])->label('Ticket Date') ?>
            </div>
        <!-- ------------------------------------------------------------------------------------------------------- -->
        <?php  Pjax::begin(['id'=>'pjax-booking-form3','enablePushState'=>false]);     ?>
        <!-- ------------------------------------------------------------------------------------------------------- -->
            <div class="form-group well">
                <!-- Size -->
                <?= $form->field($modelOrders, 'size')->widget(RangeInput::classname(), [
                    'options' => ['placeholder' => 'Rate (1 - 5)...'],
                    'html5Options' => ['min' => 1, 'max' => $ticketAvailable],
                    'addon' => ['append' => ['content' => 'ticket(s)']]
                ]);
                ?>
            </div>
            <!-- ------------------------------------------------------------------------------------------------------- -->
            <div class="form-group well">
                <!-- Coupon -->
                <?= $form->field($modelOrders, 'coupon_code')->dropDownList($coupons,
                    [
                        'id' => 'form-coupon-drop',
                        'class'=>'form-control',
                        'prompt' => 'Select Coupon...'
                    ])->label('Coupon') ?>
            </div>
            <!-- ------------------------------------------------------------------------------------------------------- -->
            <div class="form-group well">
                <!-- Variant -->
                <?= $form->field($modelTickedBookedBuy, 'variant_id')->dropDownList($variants,
                    [
                        'id' => 'form-coupon-drop',
                        'class'=>'form-control',
                        'prompt' => 'Select Variant...'
                    ])->label('Variant') ?>
            </div>
            <!-- ------------------------------------------------------------------------------------------------------- -->
            <div class="form-group well">
                <!-- Extras -->
                <?= $form->field($modelTickedBookedBuy, 'extra_id')->dropDownList($extras,
                    [
                        'id' => 'form-coupon-drop',
                        'class'=>'form-control',
                        'prompt' => 'Select Extras...'
                    ])->label('Extras') ?>
            </div>
            <!-- ------------------------------------------------------------------------------------------------------- -->
            <div class="form-group well">
                <!-- Question -->
                <?= $form->field($modelTickedBookedBuy, 'question_id')->dropDownList($questions,
                    [
                        'id' => 'form-coupon-drop',
                        'class'=>'form-control',
                        'prompt' => 'Select Question...'
                    ])->label('Question') ?>
                <!-- Answer -->
                <?= $form->field($modelAnswer, 'answer')->textInput(['maxlength' => true, 'value'=> '']) ?>
            </div>
        <!-- ------------------------------------------------------------------------------------------------------- -->
        <?php Pjax::end(); ?>

        <?php Pjax::end(); ?>

        <?php Pjax::end(); ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<?php $this->registerJs('

');
