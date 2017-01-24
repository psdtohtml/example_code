<?php
/**
 * @var \yii\web\View $this
 * @var \app\models\Tax $model
 * @var array $dataProvider
 */
$this->title = Yii::t('app', 'Manage Options')
?>
    <h1><?= yii\helpers\Html::encode($this->title) ?></h1>

<?php \yii\widgets\Pjax::begin([
        'id' => 'pjax-tax-grid',
]) ?>
    <div class="form-group well">
        <?=
        $this->render('partials/_manageTax', [
            'model'        => $model,
            'dataProvider' => $dataProvider,
        ]); ?>
    </div>
<?php \yii\widgets\Pjax::end(); ?>

<?= \kfosoft\yii2\system\OptionsWidget::widget(); ?>