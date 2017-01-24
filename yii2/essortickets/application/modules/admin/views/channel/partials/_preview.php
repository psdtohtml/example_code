<?php
/**
 * @var yii\web\View $this
 * @var app\models\Channel $model
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use \yii\helpers\Html;
use yii\helpers\Url;

?>
<h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Preview')) ?></h4>
<?= Html::label((string)$model->widget_code) ?> <br/>