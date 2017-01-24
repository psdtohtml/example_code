<?php
/**
 * @var yii\web\View $this
 * @var $dataProviderTicket
 * @var $modelTicket
 * @var $peopleSize
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use app\enums\OrderType;
use app\enums\Status;
use kartik\grid\GridView;
use \yii\helpers\Html;
use yii\widgets\Pjax;

?>

<h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Tickets')) ?></h4>
<?= yii\helpers\Html::encode('People' . ' ' . $peopleSize) ?>

<?php Pjax::begin(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProviderTicket,
    'summary'=>'',
    'columns' => [
        [
            'label' =>$modelTicket->getAttributeLabel('id'),
            'attribute' => 'id',
            'value' => function ($modelTicket) {
                if(isset($modelTicket['id'])) {
                    return  OrderType::labelById(OrderType::TICKET) . '-' . $modelTicket['id'];
                }else{
                    return '-';
                }}
        ],
        [
            'label' =>$modelTicket->getAttributeLabel('product'),
            'attribute' => 'product',
            'value' => 'product',
        ],
        [
            'label' =>$modelTicket->getAttributeLabel('tier'),
            'attribute' => 'tier',
            'value' => 'tier',
        ],
        [
            'label' =>$modelTicket->getAttributeLabel('price'),
            'attribute' => 'price',
            'value' => 'price',
        ],
    ],
]); ?>

<?php Pjax::end(); ?>
