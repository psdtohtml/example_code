<?php
/**
 * @var yii\web\View $this
 * @var app\models\Orders $model
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use app\enums\OrderType;
use app\enums\Status;
use \yii\helpers\Html;

?>
<h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Order information')) ?></h4>
<?= Html::activeLabel($model, 'valid_from') . ": {$model->valid_from}" ?> <br/>
<?= Html::activeLabel($model, 'status') . ": " . Status::getStatusById($model->status) ?> <br/>
<?= Html::activeLabel($model, 'size') . ": {$model->size}" ?> <br/>
