<?php
namespace app\base;

use Yii;
use yii\console\Controller;

/**
 * Base command.
 * @package app\base
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Command extends Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        Yii::$app->language = 'en';
    }

    /**
     * Find process.
     * @param string $process di process for find.
     * @param callable|null $callable function for use before check it works.
     * @param int $timeout timeout.
     * @return bool
     */
    public function findProcess($process, callable $callable = null, $timeout = 5)
    {
        $result = false;
        $i = $timeout;
        while ($i--) {
            !is_null($callable) && $callable($i, $timeout);
            exec('ps -ax | grep ' . $process . ' | grep -v grep', $haveProcess);
            if ($haveProcess) {
                $result = true;
                break;
            }
            sleep(1);
        }

        return $result;
    }
}
