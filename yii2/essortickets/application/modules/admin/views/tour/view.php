<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\search\Tour */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Tour', 'url' => ['manage']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tax-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= yii\helpers\Html::button( \Yii::t('app', 'Update'), [
            'value' => Url::to(['update', 'id' => $model->id]),
            'title' => 'Update',
            'class' => 'showModalButton btn btn-primary',
            'id'    => 'submit-add-booking'
        ]);

        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'attribute'=>'logo',
                'value'=> Yii::$app->request->baseUrl . '/uploads/' . $model->logo,
                'format' => ['image',['width'=>'100','height'=>'100']],
            ],
            [
                'label'     => $model->getAttributeLabel('manager'),
                'attribute' => 'manager',
                'value'     => $manager,
            ],
            'description',
            [
                'label'     => $model->getAttributeLabel('recurring'),
                'attribute' => 'recurring',
                'format' => 'raw',
                'value'=> yii\helpers\BaseStringHelper::truncate(\app\enums\EventRecurring::getTitleByType($model['recurring']), 80),

            ],
            [
                'label'     => $model->getAttributeLabel('currency'),
                'attribute' => 'currency',
                'format' => 'raw',
                'value'=> \app\enums\Currency::getCurrencySymbol((string)\app\enums\Currency::getTitleByType($model['currency'])),

            ],
            'e_phone',
            'meeting_point',
            'currency',
            'ticket_available',
            'ticket_booking_available',
            'ticket_minimum',
            'time_zone',
            'start_tour_date',
            'end_tour_date',
            'start_tour_time',
            'end_tour_time',
            'customer_ticket_limit',
            'notice',
            [
                'label'     => $model->getAttributeLabel('link_info'),
                'attribute' => 'link_info',
                'format' => 'raw',
                'value'=> base64_decode($model['link_info']),
            ],
            [
                'label'     => $model->getAttributeLabel('tax'),
                'attribute' => 'tax',
                'value'     => $tax,
            ],
        ],
    ]) ?>

</div>

<div class="form-group well">
    <?php $form = ActiveForm::begin([
            'action' => Url::to(['/admin/tour/confirmation-mail/', 'id' => $model->id]),
            'id' => 'confirmation-mail-form'
    ]); ?>
    <?= $form->field($model, 'confirmation_mail')->textArea(['rows' => '10']) ?>
    <?= Html::submitButton('Save') ?>
    <?php ActiveForm::end(); ?>
</div>

