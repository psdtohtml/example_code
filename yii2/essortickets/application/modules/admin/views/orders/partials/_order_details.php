<?php
/**
 * @var yii\web\View $this
 * @var $username
 * @var app\models\Orders $model
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use app\enums\Status;
use \yii\helpers\Html;
use yii\helpers\Url;

?>
<h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Order information')) ?></h4>

<?php if(!is_null($model->total_price)) : echo Html::activeLabel($model, 'total_price') . ": " . $model->total_price . \app\enums\Currency::getCurrencySymbol((string)\app\enums\Currency::getTitleByType(Yii::$app->tour->getCurrencyByOrderId($model->id))); else : echo Html::activeLabel($model, 'total_price') . ': '. '-'; endif; ?> <br/>
<?php if(isset($username)) : echo Html::label(\Yii::t('app', 'User')) . ': '. $username; else : echo Html::label(\Yii::t('app', 'User')) . ': '. '-'; endif; ?> <br/>
<?php if(isset($customer)) : echo Html::label(\Yii::t('app', 'Customer')) . ': '. Html::a(Html::encode($customer),['/admin/customer/manage-customer', 'id' => $model->customer_id]); else : echo Html::label(\Yii::t('app', 'Customer')) . ': '. '-'; endif; ?> <br/>
<?= Html::activeLabel($model, 'status') . ": " . Status::getStatusById($model->status) ?> <br/>
<?= Html::activeLabel($model, 'size') . ": {$model->size}" ?> <br/>
<?php if(!is_null($model->coupon_code)) : echo Html::activeLabel($model, 'coupon_code') . ': '. $model->coupon_code; else : echo Html::activeLabel($model, 'coupon_code') . ': '. '-'; endif; ?> <br/>
<?= Html::activeLabel($model, 'confirm') . ': ' .\app\enums\YesNo::labelById($model->confirm) ?> <br/>
<?= Html::activeLabel($model, 'valid_from') . ": {$model->valid_from}" ?> <br/>

<p>
    <?=
    yii\helpers\Html::button( \Yii::t('app', 'Edit'), [
        'value' => Url::to(['edit-order', 'id' => $model->id]),
        'title' => 'Edit Order',
        'class' => 'showModalButton btn btn-primary',
        'id'    => 'submit-edit-order'
    ]);
    ?>
</p>
