<?php
namespace app\enums;

use \Yii;
use app\enums\base\Enum;

/**
 * Currency Enum
 * @package app\enums
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Currency extends Enum
{
    const GBP  = 0;
    const EUR = 1;
    const USD  = 2;

    /**
     * List data.
     * @return array|null data.
     */
    public static function listData()
    {
        return [
            self::GBP  => Yii::t('app', 'GBP'),
            self::USD  => Yii::t('app', 'USD'),
            self::EUR => Yii::t('app', 'EUR'),
        ];
    }

    /**
     * @param int $type see self consts.
     * @return null
     */
    public static function getTitleByType($type)
    {
        $list = static::listData();
        return isset($list[$type]) ? $list[$type] : null;
    }

    /**
     * @param $currencyCode
     * @param string $locale
     * @return string
     */
    public static function getCurrencySymbol($currencyCode, $locale = 'en_US')
    {
        $formatter = new \NumberFormatter($locale . '@currency=' . $currencyCode, \NumberFormatter::CURRENCY);
        return $formatter->getSymbol(\NumberFormatter::CURRENCY_SYMBOL);
    }

}

