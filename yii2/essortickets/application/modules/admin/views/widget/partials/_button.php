<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<p>
    <?=
    yii\helpers\Html::button(\Yii::t('app', base64_decode($label)),
        [
            'class'=>'btn btn-success',
            'onclick'=>"window.location.href = '" . Url::to(['form','id'=>$id]) . "';",
            'data-toggle'=>'tooltip',
            'title'=>Yii::t('app', 'Book Now'),
        ]
    )
        ?>
</p>


<?php $this->registerCss('
.btn-success {
    color: #fff;
    background-color: ' . base64_decode($color) . ';
    border-color: #4cae4c;
}
.btn {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: normal;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
}
');?>
