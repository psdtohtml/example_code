<?php
/**
 * @var yii\web\View $this
 * @var app\models\Channel $model
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use \yii\helpers\Html;
\Eddmash\Clipboard\ClipboardAsset::register($this);
?>
<h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Widget Code')) ?></h4>
<?= \Eddmash\Clipboard\Clipboard::widget([
'model' => $model,
'attribute' => 'widget_code',
'options'=>['readonly'=>""],
'action' => 'copy',
]);?>
