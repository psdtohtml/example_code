<?php
/**
 * @var yii\web\View $this
 * @var app\models\Customer $model
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use \yii\helpers\Html;

?>
<h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Booking information')) ?></h4>

<?= Html::activeLabel($model, 'title') . ': ' . app\enums\TitleCustomer::getTitleById($model->title) ?> <br/>
<?= Html::activeLabel($model, 'name') . ": {$model->name}" ?> <br/>
<?= Html::activeLabel($model, 'last_name') . ": {$model->last_name}" ?> <br/>
<?= Html::activeLabel($model, 'company') . ": {$model->company}" ?> <br/>
<?= Html::activeLabel($model, 'email') . ": {$model->email}" ?> <br/>
<?= Html::activeLabel($model, 'phone') . ": {$model->phone}" ?> <br/>
<?= Html::activeLabel($model, 'subscribed') . ': ' . app\enums\YesNo::labelById($model->subscribed) ?> <br/>
<?= Html::label(\Yii::t('app', 'Total Spent Money')) . ': '. $totalSpentMoney; ?> <br/>