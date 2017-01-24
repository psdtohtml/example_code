<?php
namespace app\enums;

use \Yii;
use app\enums\base\Enum;

/**
 * EventRecurring Enum
 * @package app\enums
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class EventRecurring extends Enum
{
    const MANUAL_EDITING = 0;
    const ONCE           = 1;
    const DAILY          = 2;
    const EVERY_WEEKDAY  = 3;
    const EVERY_WEEKEND  = 4;

    /**
     * List data.
     * @return array|null data.
     */
    public static function listData()
    {
        return [
            self::MANUAL_EDITING => Yii::t('app', 'Manual editing(Manual event creation).'),
            self::ONCE           => Yii::t('app', 'Once.'),
            self::DAILY          => Yii::t('app', 'Every day.'),
            self::EVERY_WEEKDAY  => Yii::t('app', 'Every weekday(Monday to Friday).'),
            self::EVERY_WEEKEND  => Yii::t('app', 'Every weekend(Saturday to Sunday).'),
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

}

