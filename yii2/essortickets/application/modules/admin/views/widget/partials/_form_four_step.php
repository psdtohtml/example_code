<?php
/**
 * @var yii\web\View $this
 * @var app\models\Orders $model
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
use app\enums\Status;
use \yii\helpers\Html;
?>
<h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Order information')) ?></h4>
<?php if(isset($startDayOfWeek)) : echo Html::label(\Yii::t('app', 'Day of week')) . ': '. $startDayOfWeek; else : echo Html::label(\Yii::t('app', 'Day of week')) . ': '. '-'; endif; ?> <br/>
<?php if(isset($startDate)) : echo Html::label(\Yii::t('app', 'Start Date')) . ': '. $startDate; else : echo Html::label(\Yii::t('app', 'Start Date')) . ': '. '-'; endif; ?> <br/>
<?php if(isset($startTime)) : echo Html::label(\Yii::t('app', 'Start Time')) . ': '. $startTime; else : echo Html::label(\Yii::t('app', 'Start Time')) . ': '. '-'; endif; ?> <br/>

<h4><?= yii\helpers\Html::encode(\Yii::t('app', 'Tickets')) ?></h4>
<?php if(isset($tickets)){
    $totalPrice = 0;
    foreach ($tickets as $key => $value){
        if($value != 0) {
            $totalPrice = $totalPrice + ($value * Yii::$app->ticket->getTicketPriceByTiersNameAndTourId(base64_decode($key), $id));

            if($included === false && $cumulative == true){
                $tiersPrice = (($value * Yii::$app->ticket->getTicketPriceByTiersNameAndTourId(base64_decode($key), $id)) + ((($value * Yii::$app->ticket->getTicketPriceByTiersNameAndTourId(base64_decode($key), $id)) / 100) * $tax));
            }else{
                $tiersPrice = $value * Yii::$app->ticket->getTicketPriceByTiersNameAndTourId(base64_decode($key), $id);
            }

            echo Html::label(\Yii::t('app', base64_decode($key))) . ' x ' . $value . ' = ' . $tiersPrice . ' ' . \app\enums\Currency::getCurrencySymbol(Yii::$app->tour->getCurrencyForWidgetByTourId($id));
            echo '<br/>';
            $events;
        }
    }
}else{
    echo '<br/>';
    echo Html::label(\Yii::t('app', 'No tickets selected!'));
}
?>
<?php if($checkEmptyExtra === true){
    echo '<h4>' . yii\helpers\Html::encode(\Yii::t('app', 'Extras')).'</h4>';
    foreach ($extras as $key => $value){
        if($value != 0){
            $totalPrice = $totalPrice + ($value * Yii::$app->extras->getExtrasById($key)->price);

            if($included === false && $cumulative == true){
                $extraPrice = (($value * Yii::$app->extras->getExtrasById($key)->price) + ((($value * Yii::$app->extras->getExtrasById($key)->price) / 100) * $tax));
            }else{
                $extraPrice = $value * Yii::$app->extras->getExtrasById($key)->price;
            }
            echo Html::label(Yii::$app->extras->getExtrasById($key)->name) . ' x '. $value . ' = ' . $extraPrice . ' ' . \app\enums\Currency::getCurrencySymbol(Yii::$app->tour->getCurrencyForWidgetByTourId($id));
            echo '<br/>';
        }
    }
}else{
    echo '<br/>';
    echo Html::label(\Yii::t('app', 'No Extras selected!'));
}
?>

<h4><?= yii\helpers\Html::encode(\Yii::t('app', 'To pay')) ?></h4>
<?php
if($included === true){
    //Total price
    if(isset($totalPrice)) {
        echo Html::label(\Yii::t('app', 'Total price')) . ': ' . ($resultTotalPrice = $totalPrice) . ' ' . \app\enums\Currency::getCurrencySymbol(Yii::$app->tour->getCurrencyForWidgetByTourId($id));
    }else{
        echo Html::label(\Yii::t('app', 'Total price')) . ': '. '-';
    }
    echo '<br/>';
}else{
    if($cumulative === true){
        //Total price
        if(isset($totalPrice) && isset($tax)){
            echo Html::label(\Yii::t('app', 'Total price')) . ': '. ($resultTotalPrice = $totalPrice + (($totalPrice / 100) * $tax)) . ' ' . \app\enums\Currency::getCurrencySymbol(Yii::$app->tour->getCurrencyForWidgetByTourId($id));
        } else {
            echo Html::label(\Yii::t('app', 'Total price')) . ': '. '-';
        }
        echo '<br/>';
    }else{
        //Price
        if(isset($totalPrice)) {
            echo Html::label(\Yii::t('app', 'Price')) . ': ' . ($resultTotalPrice = $totalPrice) . ' ' . \app\enums\Currency::getCurrencySymbol(Yii::$app->tour->getCurrencyForWidgetByTourId($id));
        }else{
            echo Html::label(\Yii::t('app', 'Price')) . ': '. '-';
        }
        echo '<br/>';
        //Tax
        if(isset($tax)){
            echo Html::label(\Yii::t('app', 'Tax')) . ': '. ($totalPrice / 100) * $tax . ' ' . \app\enums\Currency::getCurrencySymbol(Yii::$app->tour->getCurrencyForWidgetByTourId($id));
        } else {
            echo Html::label(\Yii::t('app', 'Tax')) . ': '. '-';
        }
        echo '<br/>';
        //Total price
        if(isset($totalPrice) && isset($tax)){
            echo Html::label(\Yii::t('app', 'Total price')) . ': '. ($resultTotalPrice = $totalPrice + (($totalPrice / 100) * $tax)) . ' ' . \app\enums\Currency::getCurrencySymbol(Yii::$app->tour->getCurrencyForWidgetByTourId($id));
        } else {
            echo Html::label(\Yii::t('app', 'Total price')) . ': '. '-';
        }
        echo '<br/>';
    }
}

?>
<br/>
<div class="right">
        <?= Html::a(\Yii::t('app', 'Next'), ['five-step', 'price' => $resultTotalPrice],
            ['class' => 'btn btn-primary',]) ?>
</div>


<?php $this->registerCss('

.form-control {
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
    -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
}
.btn-primary {
    color: #fff;
    background-color: #337ab7;
    border-color: #2e6da4;
}

.label {
    display: inline-block;
    max-width: 100%;
    margin-bottom: 5px;
    font-weight: bold;
}

.btn-success {
    color: #fff;
    background-color: #5cb85c;
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

.has-error .help-block, .has-error .control-label, .has-error .radio, .has-error .checkbox, .has-error .radio-inline, .has-error .checkbox-inline, .has-error.radio label, .has-error.checkbox label, .has-error.radio-inline label, .has-error.checkbox-inline label {
    color: #a94442;
}
.help-block {
    display: block;
    margin-top: 5px;
    margin-bottom: 10px;
    color: #737373;
}

.help-block-error {
    display: block;
    margin-top: 5px;
    margin-bottom: 10px;
    color: #a94442;
}


/*
	 CSS-Tricks Example
	 by Chris Coyier
	 http://css-tricks.com
*/

#page-wrap {
  width: 500px;
  margin: 100px auto;
}

.input-tiers {
  float: left;
  width: 40px;
  font: bold 20px Helvetica, sans-serif;
  padding: 3px 0 0 0;
  text-align: center;
}
form div {
  overflow: hidden;
  margin: 0 0 5px 0;
}
.button {
  margin: 0 0 0 5px;
  text-indent: -9999px;
  cursor: pointer;
  width: 29px;
  height: 29px;
  float: left;
  text-align: center;
  background: url(https://css-tricks.com/examples/InputNumberIncrementer/images/buttons.png) no-repeat;
}
.dec {
  background-position: 0 -29px;
}

.buttons {
  padding: 20px 0 0 140px;
}
');
