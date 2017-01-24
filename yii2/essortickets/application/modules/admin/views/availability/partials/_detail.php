<?php
/**
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model \app\modules\admin\models\search\TimeDetail
 * @var int $modelCode Model code for suggestions
 * @var $autoCompleteLimit
 * @var $availabilityId
 *
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use yii\bootstrap\Modal;
use yii\grid\DataColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

?>
<div class="well">

    <?php Pjax::begin([
        'id' => 'pjax-form'
    ]);?>

    <?= GridView::widget([
        'summary'=>'',
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'time',
                'label' => 'Time',
                'format' => 'raw',
                'value'=>function ($data) use ($availabilityId) {
                    return Html::button( $data['time'], [
                        'value' => Url::to(['update-time', 'time' => $data['time'], 'availabilityId' => $availabilityId]),
                        'title' => 'Update Time',
                        'class' => 'showModalButton btn btn-primary',
                    ]).    Html::a(
                        '<span class="glyphicon glyphicon-trash"></span>',
                        Url::to(['delete-time', 'time' => $data['time'], 'availabilityId' => $availabilityId]),
                        [
                            'data' => [
                                'confirm' => \Yii::t('app', 'Delete detail?'),
                                'data-method' => 'GET',
                            ],
                        ]
                    );
                },
            ],
            [
                'attribute' => 'monday',
                'label' => 'Monday',
                'format' => 'raw',
                'value'=>function ($data) use ($availabilityId) {
                    if($data['monday'] == 0){
                        $title = '+';
                    }else{
                        $title = $data['monday'];
                    }
                    return Html::button( $title, [
                        'value' => Url::to(['update-day',
                            'time' => $data['time'],
                            'day' => \app\enums\Days::MONDAY,
                            'availabilityId' => $availabilityId
                        ]),
                        'title' => 'Update Monday',
                        'class' => 'showModalButton btn btn-primary',
                    ]);
                },
            ],
            [
                'attribute' => 'tuesday',
                'label' => 'Tuesday',
                'format' => 'raw',
                'value'=>function ($data) use ($availabilityId) {
                    if($data['tuesday'] == 0){
                        $title = '+';
                    }else{
                        $title = $data['tuesday'];
                    }
                    return Html::button( $title, [
                        'value' => Url::to(['update-day',
                            'time' => $data['time'],
                            'day' => \app\enums\Days::TUESDAY,
                            'availabilityId' => $availabilityId
                        ]),
                        'title' => 'Update Tuesday',
                        'class' => 'showModalButton btn btn-primary',
                    ]);
                },
            ],
            [
                'attribute' => 'wednesday',
                'label' => 'Wednesday',
                'format' => 'raw',
                'value'=>function ($data) use ($availabilityId) {
                    if($data['wednesday'] == 0){
                        $title = '+';
                    }else{
                        $title = $data['wednesday'];
                    }
                    return Html::button( $title, [
                        'value' => Url::to(['update-day',
                            'time' => $data['time'],
                            'day' => \app\enums\Days::WEDNESDAY,
                            'availabilityId' => $availabilityId
                        ]),
                        'title' => 'Update Wednesday',
                        'class' => 'showModalButton btn btn-primary',
                    ]);
                },
            ],
            [
                'attribute' => 'thursday',
                'label' => 'Thursday',
                'format' => 'raw',
                'value'=>function ($data) use ($availabilityId) {
                    if($data['thursday'] == 0){
                        $title = '+';
                    }else{
                        $title = $data['thursday'];
                    }
                    return Html::button( $title, [
                        'value' => Url::to(['update-day',
                            'time' => $data['time'],
                            'day' => \app\enums\Days::THURSDAY,
                            'availabilityId' => $availabilityId
                        ]),
                        'title' => 'Update Thursday',
                        'class' => 'showModalButton btn btn-primary',
                    ]);
                },
            ],
            [
                'attribute' => 'friday',
                'label' => 'Friday',
                'format' => 'raw',
                'value'=>function ($data) use ($availabilityId) {
                    if($data['friday'] == 0){
                        $title = '+';
                    }else{
                        $title = $data['friday'];
                    }
                    return Html::button( $title, [
                        'value' => Url::to(['update-day',
                            'time' => $data['time'],
                            'day' => \app\enums\Days::FRIDAY,
                            'availabilityId' => $availabilityId
                        ]),
                        'title' => 'Update Friday',
                        'class' => 'showModalButton btn btn-primary',
                    ]);
                },
            ],
            [
                'attribute' => 'saturday',
                'label' => 'Saturday',
                'format' => 'raw',
                'value'=>function ($data) use ($availabilityId) {
                    if($data['saturday'] == 0){
                        $title = '+';
                    }else{
                        $title = $data['saturday'];
                    }
                    return Html::button( $title, [
                        'value' => Url::to(['update-day',
                            'time' => $data['time'],
                            'day' => \app\enums\Days::SATURDAY,
                            'availabilityId' => $availabilityId
                        ]),
                        'title' => 'Update Saturday',
                        'class' => 'showModalButton btn btn-primary',
                    ]);
                },
            ],
            [
                'attribute' => 'sunday',
                'label' => 'Sunday',
                'format' => 'raw',
                'value'=>function ($data) use ($availabilityId) {
                    if($data['sunday'] == 0){
                        $title = '+';
                    }else{
                        $title = $data['sunday'];
                    }
                    return Html::button( $title, [
                        'value' => Url::to(['update-day',
                            'time' => $data['time'],
                            'day' => \app\enums\Days::SUNDAY,
                            'availabilityId' => $availabilityId]),
                        'title' => 'Update Sunday',
                        'class' => 'showModalButton btn btn-primary',
                    ]);
                },
            ],
        ],
    ]);
    ?>
    <?=
    yii\helpers\Html::button( \Yii::t('app', 'Add Time'), [
        'value' => Url::to(['create-time', 'id'=>$availabilityId]),
        'title' => 'Add Time',
        'class' => 'showModalButton btn btn-success',
        'id'    => 'submit-create-extras'
    ]);
    ?>
</div>

<?php Pjax::end();?>
