<?php
/**
 * @var yii\web\View $this
 * @var app\models\Customer $model
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use \yii\helpers\Html;

?>
<h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Address')) ?></h4>

<?= Html::activeLabel($model, 'country') . ": {$model->country}" ?> <br/>
<?= Html::activeLabel($model, 'city') . ": {$model->city}" ?> <br/>
<?= Html::activeLabel($model, 'street_address') . ": {$model->street_address}" ?> <br/>
<?= Html::activeLabel($model, 'zip') . ": {$model->zip}" ?> <br/>
<?= Html::activeLabel($model, 'ip') . ": {$model->ip}" ?> <br/>