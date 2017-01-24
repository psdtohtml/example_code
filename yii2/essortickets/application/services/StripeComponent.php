<?php
namespace app\services;

use Error;
use Exception;
use Stripe\Charge;
use Stripe\Stripe;
use Yii;
use yii\base\Component;

/**
 * Class StripeComponent
 * @package app\services
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class StripeComponent extends Component
{

    /**
     * @param $amount
     * @param $currency
     * @param $source
     * @param $description
     * @param $idempotency_key
     *
     * @return object
     */
    public function checkout($amount, $currency, $source, $description, $idempotency_key)
    {
        try {
            Stripe::setApiKey(Yii::$app->option_component->getOptionByKey('stripe.sk'));
            $charge = \Stripe\Charge::create(array(
                "amount" => $amount,
                "currency" => $currency,
                "source" => $source, // obtained with Stripe.js
                "description" => $description
            ), array(
                "idempotency_key" => $idempotency_key,
            ));
            return $charge;
        } catch(\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err  = $body['error'];

            print('Status is:' . $e->getHttpStatus() . "\n");
            print('Type is:' . $err['type'] . "\n");
            print('Code is:' . $err['code'] . "\n");
            // param is '' in this case
            print('Param is:' . $err['param'] . "\n");
            print('Message is:' . $err['message'] . "\n");
        } catch (\Stripe\Error\RateLimit $e) {
            // Too many requests made to the API too quickly
        } catch (\Stripe\Error\InvalidRequest $e) {
            // Invalid parameters were supplied to Stripe's API
        } catch (\Stripe\Error\Authentication $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
        } catch (\Stripe\Error\ApiConnection $e) {
            // Network communication with Stripe failed
        } catch (\Stripe\Error\Base $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
        } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe
        }
    }

    /**
     * @param string $ch
     */
    public function refund($ch)
    {
        \Stripe\Stripe::setApiKey(Yii::$app->option_component->getOptionByKey('stripe.sk'));
        $re = \Stripe\Refund::create(array("charge" => $ch));
        var_dump($re);
    }

    /**
     * @param $amount
     * @return string
     * @throws \Error
     */
    public function getAmountForStripe($amount)
    {
        if(!isset($amount)){
            throw new Error("Error stripe amount!");
        }elseif(stristr($amount, '.') === FALSE) {
            return $amount . '00';
        }else{
            $arr = explode('.', $amount);
            if(iconv_strlen($arr[1]) == 2 ){
               return str_replace('.', '',$amount);
            }else{
               return $amount . '0';
            }
        }
    }
}