<?php
namespace app\commands;

use app\base\Command;
use app\enums\MessageStatus;
use app\models\Message as MessageModel;
use DateTime;
use DateTimeZone;
use Yii;

/**
 * Delivery Controller.
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class DeliveryController extends Command
{
    /** @var string $viewTemplate */
    public $viewTemplate = 'customer_custom_message';

    /**
     * @return bool
     */
    public function actionDeliveryToCustomersWaiting()
    {
        /** @var string $now date time */
        $now =(new DateTime('now', new DateTimeZone('Europe/Kiev')))->format('Y-m-d H:i:s');

        $models = new MessageModel();
        $models = $models::findAll(['status' => MessageStatus::WAITING]);

        if($models !== null){
            foreach ($models as $model){
                $username = Yii::$app->customer->getCustomerFullNameById($model->customer_id);
                /** @var \app\models\Message $model */
                if($model->status == MessageStatus::WAITING){
                    if($this->send(
                        Yii::$app->customer->getCustomerEmailById($model->customer_id),
                        $model->head,
                        $this->viewTemplate,
                        [
                            'username'  => $username,
                            'content'   => $model->body,
                        ],
                        //Path to PDF file
                        (string)(Yii::$app->basePath . '/../web/uploads/' . $model->attachment)
                    )){
                        $model->status = MessageStatus::SENT;
                        $model->datetime_send = $now;
                        $model->save(false);
                    }else{
                        $model->status = MessageStatus::NOT_DELIVERED;
                        $model->save(false);
                    }
                }else{
                    return false;
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function actionDeliveryToCustomersNotDelivered()
    {
        /** @var string $now date time */
        $now =(new DateTime('now', new DateTimeZone('Europe/Kiev')))->format('Y-m-d H:i:s');

        $models = new MessageModel();
        $models = $models::findAll(['status' => MessageStatus::NOT_DELIVERED]);
        if($models !== null) {
            foreach ($models as $model) {
                $username = Yii::$app->customer->getCustomerFullNameById($model->customer_id);
                /** @var \app\models\Message $model */
                if ($model->status == MessageStatus::NOT_DELIVERED) {
                    if ($this->send(
                        Yii::$app->customer->getCustomerEmailById($model->customer_id),
                        $model->head,
                        $this->viewTemplate,
                        [
                            'username' => $username,
                            'content' => $model->body,
                        ],
                        //Path to PDF file
                        (string)(Yii::$app->basePath . '/../web/uploads/' . $model->attachment)
                    )
                    ) {
                        $model->status = MessageStatus::SENT;
                        $model->datetime_send = $now;
                        $model->save(false);
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $view yii view path.
     * @param array $viewParams
     * @return bool
     */
    public function send($to, $subject, $view, array $viewParams = [], $attachment = null)
    {
        if($attachment !== null){
            return \Yii::$app->mailer->compose($view, $viewParams)
                ->setTo($to)
                ->setFrom([Yii::$app->params['info.email'] => \Yii::$app->params['admin.alias']])
                ->setSubject($subject)
                ->attach($attachment)
                ->send();
        }else{
            return \Yii::$app->mailer->compose($view, $viewParams)
                ->setTo($to)
                ->setFrom([Yii::$app->params['info.email'] => \Yii::$app->params['admin.alias']])
                ->setSubject($subject)
                ->send();
        }

    }

}