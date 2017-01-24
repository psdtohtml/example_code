<?php

use app\enums\Status;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProviderMessages \app\services\Message */
/* @var $modelMessages app\models\Message */

?>
<?= GridView::widget([
    'dataProvider' => $dataProviderMessages,
    'summary'=>'',
    'columns' => [
        'head',
        'body',
        [
            'label' => $modelMessages->getAttributeLabel('status'),
            'attribute' => 'status',
            'format' => 'raw',
            'value'=>function ($modelMessages) {
                return yii\helpers\BaseStringHelper::truncate(\app\enums\MessageStatus::getStatusById($modelMessages['status']), 80);
            }
        ],
        'datetime_send',
    ],
]); ?>
