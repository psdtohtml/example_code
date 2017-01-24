<?php
namespace app\enums;

use app\models\Availability as AvailabilityModel;
use app\models\Booking;
use app\models\Detail;
use app\models\Question;
use app\models\Ticket;
use app\models\Variant;
use app\models\Extras;
use \Yii;
use app\enums\base\Enum;

/**
 * Models Enum
 * @package app\enums
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Models extends Enum
{
    const TICKET       = '1';
    const VARIANT      = '2';
    const EXTRAS       = '3';
    const QUESTION     = '4';
    const AVAILABILITY = '5';
    const DETAIL       = '6';
    const BOOKING      = '7';

    /**
     * List data.
     * @return array|null data.
     */
    public static function listData()
    {
        return [
            self::TICKET       => Ticket::className(),
            self::VARIANT      => Variant::className(),
            self::EXTRAS       => Extras::className(),
            self::QUESTION     => Question::className(),
            self::AVAILABILITY => AvailabilityModel::className(),
            self::DETAIL       => Detail::className(),
            self::BOOKING      => Booking::className(),
        ];
    }

    public static function listModels()
    {
        return [
            self::TICKET       => new Ticket(),
            self::VARIANT      => new Variant(),
            self::EXTRAS       => new Extras(),
            self::QUESTION     => new Question(),
            self::AVAILABILITY => new AvailabilityModel(),
            self::DETAIL       => new Detail(),
            self::BOOKING      => new Booking(),
        ];
    }

    /**
     * @param int $type see self consts.
     * @return null
     */
    public static function getDataByType($type)
    {
        $list = static::listData();
        return isset($list[$type]) ? $list[$type] : null;
    }

    /**
     * @param int $type see self consts.
     * @return null
     */
    public static function getModelsByType($type)
    {
        $list = static::listModels();
        return isset($list[$type]) ? $list[$type] : null;
    }

}

