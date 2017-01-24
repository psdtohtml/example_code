<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\base;

use \Yii;

class PhpMessageSource extends \yii\i18n\PhpMessageSource
{
    public function getLangMessages($category, $language)
    {
        return $this->loadMessages($category, $language);
    }
}
