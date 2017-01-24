<?php
/**
 * @var yii\web\View $this
 * @var $dataProviderQuestion
 * @var $modelQuestion
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use app\enums\OrderType;
use app\enums\Status;
use kartik\grid\GridView;
use \yii\helpers\Html;
use yii\widgets\Pjax;

?>

    <h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Answers')) ?></h4>

<?php Pjax::begin(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProviderQuestion,
    'summary'=>'',
    'columns' => [
        [
            'label' =>$modelQuestion->getAttributeLabel('question'),
            'attribute' => 'question',
            'value' => 'question',
        ],
        [
            'label' =>$modelQuestion->getAttributeLabel('answer'),
            'attribute' => 'answer',
            'value' => 'answer',
        ],
    ],
]); ?>

<?php Pjax::end(); ?>