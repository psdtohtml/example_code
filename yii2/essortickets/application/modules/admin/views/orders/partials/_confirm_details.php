<?php
/**
 * @var yii\web\View $this
 * @var $username
 * @var app\models\Orders $model
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use app\enums\OrderType;
use app\enums\Status;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use \yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

?>
    <h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Confirm')) ?></h4>


<?php Pjax::begin([
    'id'=>'pjax-forms',
]); ?>

<?php $form = ActiveForm::begin([
    'id' => 'form-confirm',
    'action' => Url::to(['confirm', 'id' =>$model->id])
]); ?>

    <!-- confirm -->
<?= $form->field($model, 'confirm')->hiddenInput(['id' => 'confirm-id' . '-' . 'hidden', 'value' => 1])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Confirm' : 'Confirm', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?php Pjax::end(); ?>