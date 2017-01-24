<?php
namespace app\helpers;

/**
 * Model names provioder
 * @package app\helpers
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class ModelNamesProvider
{

    const ADMIN_TOUR_SEARCH         = 0;
    const ADMIN_VARIANT_SEARCH      = 1;
    const ADMIN_TICKET_SEARCH       = 2;
    const ADMIN_COUPON_SEARCH       = 3;
    const ADMIN_DETAIL_SEARCH       = 4;
    const ADMIN_ORDER_SEARCH        = 5;
    const ADMIN_BOOKING_SEARCH      = 6;
    const ADMIN_CUSTOMER_SEARCH     = 7;
    const ADMIN_QUESTION_SEARCH     = 8;

    /**
     * @return array Array of model names
     */
    private static function getModelNames()
    {
        return [
            self::ADMIN_TOUR_SEARCH             => \app\modules\admin\models\search\Tour::className(),
            self::ADMIN_VARIANT_SEARCH          => \app\modules\admin\models\search\Variant::className(),
            self::ADMIN_TICKET_SEARCH           => \app\modules\admin\models\search\Ticket::className(),
            self::ADMIN_COUPON_SEARCH           => \app\modules\admin\models\search\Coupon::className(),
            self::ADMIN_DETAIL_SEARCH           => \app\modules\admin\models\search\Detail::className(),
            self::ADMIN_ORDER_SEARCH          => \app\modules\admin\models\search\Orders::className(),
            self::ADMIN_BOOKING_SEARCH          => \app\modules\admin\models\search\Booking::className(),
            self::ADMIN_CUSTOMER_SEARCH         => \app\modules\admin\models\search\Customer::className(),
            self::ADMIN_QUESTION_SEARCH         => \app\modules\admin\models\search\Question::className(),
        ];
    }

    /**
     * @param int $code Code of model
     * @return string|null Name of model class
     */
    public static function getModelNameByCode($code)
    {
        return isset(self::getModelNames()[$code]) ? self::getModelNames()[$code] : null;
    }
}