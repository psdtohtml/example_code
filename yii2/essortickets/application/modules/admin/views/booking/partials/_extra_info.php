<?php
/**
 * @var yii\web\View $this
 * @var app\models\Orders $model
 * @var float $total_price app\services\Orders
 * @var float $currency app\services\Orders
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use app\enums\OrderType;
use app\enums\Status;
use kartik\grid\GridView;
use \yii\helpers\Html;
use yii\widgets\Pjax;

?>

<h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Extra')) ?></h4>

<?php Pjax::begin(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProviderTicket,
    'summary'=>'',
    'columns' => [
        [
            'label' =>$modelTicket->getAttributeLabel('extraName'),
            'attribute' => 'extraName',
            'value' => 'extraName',
        ],
        [
            'label' =>$modelTicket->getAttributeLabel('extraPrice'),
            'attribute' => 'extraPrice',
            'value' => 'extraPrice',
        ],
    ],
]); ?>

<?php Pjax::end(); ?>
