<?php

namespace app\enums;

use Yii;
use app\enums\base\Enum;

/**
 * Class Status
 * @package app\enums
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Status extends Enum
{
    const CONFIRMED   = 1;
    const EXPIRED     = 2;
    const CANCELED    = 3;
    const PENDING     = 4;
    const PROVISIONAL = 5;
    const EDITING     = 6;

    /**
     * List data.
     * @return array|null data
     */
    public static function listData()
    {
        return [
          self::CONFIRMED    => '<span style="color: green"> Confirmed </span>',
          self::EXPIRED      => '<span style="color: red"> Expired </span>',
          self::CANCELED     => '<span style="color: darkred"> Cancelled </span>',
          self::PENDING      => '<span style="color: blue"> Pending </span>',
          self::PROVISIONAL  => '<span style="color: orange"> Provisional </span>',
          self::EDITING      => '<span style="color: grey"> Editing </span>',
        ];
    }

    /**
     * @param int $id see self consts.
     * @return null
     */
    public static function getStatusById($id)
    {
        $list = static::listData();
        return isset($list[$id]) ? $list[$id] : null;
    }

    /**
     * List Countdown.
     * @return array|null data
     */
    public static function ListCountdown()
    {
        return [
            self::CONFIRMED    => 0,
            self::EXPIRED      => 0,
            self::CANCELED     => 0,
            self::PENDING      => 86400, //24h 86400
            self::PROVISIONAL  => 0,
            self::EDITING      => 600, //10m 600
        ];
    }

    /**
     * @param int $id see self consts.
     * @return null
     */
    public static function getCountdownById($id)
    {
        $list = static::ListCountdown();
        return isset($list[$id]) ? $list[$id] : null;
    }
}
