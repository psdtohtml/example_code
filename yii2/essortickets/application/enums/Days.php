<?php

namespace app\enums;

use Yii;
use app\enums\base\Enum;

/**
 * Class Days
 * @package app\enums
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Days extends Enum
{
    const MONDAY    = 1;
    const TUESDAY   = 2;
    const WEDNESDAY = 3;
    const THURSDAY  = 4;
    const FRIDAY    = 5;
    const SATURDAY  = 6;
    const SUNDAY    = 7;


    /**
     * List data.
     * @return array|null data
     */
    public static function listData()
    {
        return [
          self::MONDAY    => Yii::t('app', 'Monday'),
          self::TUESDAY   => Yii::t('app', 'Tuesday'),
          self::WEDNESDAY => Yii::t('app', 'Wednesday'),
          self::THURSDAY  => Yii::t('app', 'Thursday'),
          self::FRIDAY    => Yii::t('app', 'Friday'),
          self::SATURDAY  => Yii::t('app', 'Saturday'),
          self::SUNDAY    => Yii::t('app', 'Sunday'),
        ];
    }

    /**
     * @return array
     */
    public static function listProperty()
    {
        return [
            self::MONDAY    => 'monday',
            self::TUESDAY   => 'tuesday',
            self::WEDNESDAY => 'wednesday',
            self::THURSDAY  => 'thursday',
            self::FRIDAY    => 'friday',
            self::SATURDAY  => 'saturday',
            self::SUNDAY    => 'sunday',
        ];
    }

    /**
     * @param int $id see self consts.
     * @return null
     */
    public static function getDayById($id)
    {
        $list = static::listProperty();
        return isset($list[$id]) ? $list[$id] : null;
    }

}
