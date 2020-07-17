<?php

namespace common\exceptions\SearchResult;

use yii\base\Exception;

/**
 * Исключение возникающее при попытке запуска обновления поисковой выдачи.
 */
class ProcessAlreadyRunningException extends Exception
{
}