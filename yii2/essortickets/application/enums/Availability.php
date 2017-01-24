<?php

namespace app\enums;

use Yii;
use app\enums\base\Enum;

/**
 * Class Availability
 * @package app\enums
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Availability extends Enum
{
    const SEASON_TYPE      = 0;
    const VARIATION_TYPE   = 1;


    /**
     * List data.
     * @return array|null data
     */
    public static function listData()
    {
        return [
          self::SEASON_TYPE   => Yii::t('app', 'Season'),
          self::VARIATION_TYPE => Yii::t('app', 'Variation'),
        ];
    }

}
