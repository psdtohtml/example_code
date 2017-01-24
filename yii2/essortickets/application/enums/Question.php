<?php

namespace app\enums;

use Yii;
use app\enums\base\Enum;

/**
 * Class Question
 * @package app\enums
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Question extends Enum
{
    const MULTI_CHOICE_SELECT_TYPE   = 1;
    const TEXT_FIELD_TYPE = 2;
    const PHONE_TYPE = 3;
    const EMAIL_TYPE = 4;
    const NUMBER_TYPE = 5;
    const DATE_TYPE = 6;
    const TIME_OF_DAY_TYPE = 7;
    const DATE_AND_TIME_TYPE = 8;
    const WEBSITE_URL_TYPE = 9;

    /**
     * List data.
     * @return array|null data
     */
    public static function listData()
    {
        return [
          self::MULTI_CHOICE_SELECT_TYPE   => Yii::t('app', 'Multi choice select'),
          self::TEXT_FIELD_TYPE => Yii::t('app', 'Text field'),
          self::PHONE_TYPE => Yii::t('app', 'Phone number'),
          self::EMAIL_TYPE => Yii::t('app', 'Email'),
          self::NUMBER_TYPE => Yii::t('app', 'Number'),
          self::DATE_TYPE => Yii::t('app', 'Date'),
          self::TIME_OF_DAY_TYPE => Yii::t('app', 'Time of day'),
          self::DATE_AND_TIME_TYPE => Yii::t('app', 'Date and time'),
          self::WEBSITE_URL_TYPE => Yii::t('app', 'Website/URL'),
        ];
    }

}
