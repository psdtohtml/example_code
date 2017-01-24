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
                'attribute' => 'name',
                'label' => 'Name',
                'format' => 'raw',
                'value'=>function ($data) use ($availabilityId) {
                    if($data['name'] == NULL){
                        $title = '+';
                    }else{
                        $title = $data['name'];
                    }
                    return Html::button( $title, [
                        'value' => Url::to(['update-variation-detail',
                            'time' => $data['time'],
                            'name' => $data['name'],
                            'availabilityId' => $availabilityId]),
                        'title' => 'Update Name',
                        'class' => 'showModalButton btn btn-primary',
                    ]);
                },
            ],
            [
                'attribute' => 'time',
                'label' => 'Time',
                'format' => 'raw',
                'value'=>function ($data) use ($availabilityId) {
                    return Html::button( $data['time'], [
                        'value' => Url::to(['update-time',
                            'time' => $data['time'],
                            'availabilityId' => $availabilityId]),
                        'title' => 'Update Time',
                        'class' => 'showModalButton btn btn-primary',
                    ]);
                },
            ],
            [
                'attribute' => 'capacity',
                'label' => 'PAX',
                'format' => 'raw',
                'value'=>function ($data) use ($availabilityId) {
                    if($data['capacity'] == 0){
                        $title = '+';
                    }else{
                        $title = $data['capacity'];
                    }
                    return Html::button( $title, [
                        'value' => Url::to(['update-variation-detail',
                            'time' => $data['time'],
                            'name' => $data['name'],
                            'availabilityId' => $availabilityId
                        ]),
                        'title' => 'Update Monday',
                        'class' => 'showModalButton btn btn-primary',
                    ]);
                },
            ],

        ],
    ]);
    ?>
    <?=
    yii\helpers\Html::button( \Yii::t('app', 'Add Name'), [
        'value' => Url::to(['create-variation-detail', 'id'=>$availabilityId]),
        'title' => 'Add Name',
        'class' => 'showModalButton btn btn-success',
        'id'    => 'submit-create-variation-detail'
    ]);
    ?>
</div>

<?php Pjax::end();?>
