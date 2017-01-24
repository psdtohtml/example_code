<?php

use dosamigos\datepicker\DatePicker;
use kartik\widgets\Select2;
use kartik\widgets\TimePicker;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yiidreamteam\widgets\timezone\Picker;
use ruskid\stripe\StripeCheckoutCustom;

/* @var $this yii\web\View */
/* @var $model */
/* @var $form yii\widgets\ActiveForm */

?>

<?php $form = ActiveForm::begin([
    'id' => 'modal-form-checkout',
    'action' => '/admin/widget/six-step',
    'method' => 'POST',
]); ?>
        <script
                src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                data-key= <?= Yii::$app->option_component->getOptionByKey('stripe.pk'); ?>
                data-amount="<?= $amount; ?>"
                data-name="ESSORTICKETS"
                data-description=<?= $product;?>
                data-image=<?= $logo; ?>
                data-locale="auto"
                data-currency=<?= $currency; ?>
                data-email=<?= $email; ?>>
        </script>
<?php ActiveForm::end(); ?>