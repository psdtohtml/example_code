<?php
namespace app\services;

use Yii;
use yii\base\Component;

/**
 * Class for send to admin notify
 * @package app\services
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class AdminNotifier extends Component
{
    public $viewTemplate = 'adminNotifier/message';

    /**
     * Send error message to admin email.
     * @param string $message
     * @param string $title
     * @param int $code
     * @return bool
     */
    public function error($message, $title = 'Handled Server Error', $code = 0)
    {
        $e = new \Exception($message, $code);
        $message = "<pre>{$title} 'Exception' with message '{$message}' \n\nin {$e->getFile()} : {$e->getLine()} \n\nStack trace:\n{$e->getTraceAsString()}</pre>";

        return $this->message($title, $message);
    }

    /**
     * Send warning message to admin email.
     * @param string $message
     * @param string $title
     * @param int $code
     * @return bool
     */
    public function warning($message, $title = 'Handled Server Warning', $code = 0)
    {
        return $this->error($message, $title, $code);
    }

    /**
     * Send notify message to admin email.
     * @param string $title
     * @param $message
     * @return bool
     */
    public function message($title = 'Notify', $message)
    {
        return $this->sendServiceMail($title, $message);
    }

    /**
     * Send mail to admin email.
     * @param $subject
     * @param $message
     * @return bool
     */
    protected function sendServiceMail($subject, $message)
    {
        return Yii::$app->mailer->compose($this->viewTemplate, ['title' => $subject, 'message' => $message])
            ->setTo(Yii::$app->params['admin.email'])
            ->setFrom(Yii::$app->params['info.email'])
            ->setSubject('Server Service: ' . $subject)
            ->send();
    }
}
