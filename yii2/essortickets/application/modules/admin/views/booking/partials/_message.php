<?php
/**
 * @var yii\web\View $this
 * @var app\models\Messages $model
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use \yii\helpers\Html;

?>
<h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Message send')) . ' ' . $model->datetime_send ?></h4>
<?= Html::activeLabel($model, 'head') . ": {$model->head}" ?> <br/>
<?= Html::activeLabel($model, 'body') . ": {$model->body}" ?> <br/>
