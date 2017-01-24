<?php

namespace app\services;

use kartik\mpdf\Pdf;
use Yii;
use yii\base\Component;
use yii\helpers\Url;

/**
 * Class for implements logic with Pdf
 * @package app\services
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class PdfComponent extends Component
{

    /**
     * This method create and download ticket(s)
     * @param integer $id booking id
     * @return string|mixed
     */
    public function DownloadPdfTickets($id)
    {
        /** @var \app\models\Booking $model */
        $modelBooking = new \app\models\Booking();
        $modelBooking = $modelBooking::findOne(['id' => $id]);
        /** @var \app\modules\admin\models\search\Orders $modelOrders */
        $modelOrders = new \app\modules\admin\models\search\Orders();
        $modelOrders = $modelOrders::findOne(['id' => $modelBooking->order_id]);
        /** @var \app\models\TicketBookedBuy $modelsTicketBookedBuy */
        $modelsTicketBookedBuy = new \app\models\TicketBookedBuy();
        $modelsTicketBookedBuy = $modelsTicketBookedBuy::findAll(['order_id' => $modelOrders->id]);
        /** @var \app\models\Tour $modelTour */
        $modelTour = new \app\models\Tour();
        $modelTour = $modelTour::findOne(['name' => Yii::$app->tour->getProductNameByOrderId($modelOrders->id)]);
        /** @var \app\models\Events $modelEvent */
        $modelEvent = new \app\models\Events();
        $modelEvent = $modelEvent::findOne(['id' => $modelsTicketBookedBuy[0]->event_id]);

        $content = Yii::$app->controller->renderPartial('//' . Yii::$app->params['templatePdfUrl'] . 'common',[
            'modelTour'    => $modelTour,
            'customer'     => Yii::$app->customer->getCustomerFullNameById($modelOrders->customer_id),
            'modelOrders'  => $modelOrders,
            'modelBooking' => $modelBooking,
            'modelEvent'   => $modelEvent,
        ]);
        foreach ($modelsTicketBookedBuy as $modelTicketBookedBuy){

            /** @var \app\services\Ticket $modelTicket */
            $modelTicket = Yii::$app->ticket->findModelById($modelTicketBookedBuy->ticket_id);
            // get your HTML raw content without any layouts or scripts
            $content .= Yii::$app->controller->renderPartial('//' . Yii::$app->params['templatePdfUrl'] . 'ticket',[
                'modelBooking'         => $modelBooking,
                'modelTicketBookedBuy' => $modelTicketBookedBuy,
                'modelEvent'           => $modelEvent,
                'modelTour'            => $modelTour,
                'modelTicket'          => $modelTicket,
                'totalPrice'           => 100,
                'customer'             => Yii::$app->customer->getCustomerFullNameById($modelOrders->customer_id),
                'linkInfo'             => base64_decode($modelTour->link_info),

            ]);
        }

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            'filename' => 'Ticket(s) to ' . $modelTour->name . '.pdf',
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_DOWNLOAD,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => (string)Yii::$app->params['admin.alias']],
        ]);
        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * This method create and save in temp dir ticket(s)
     * @param integer $id order id
     * @return string|mixed
     */
    public function CreateAndSavePdfTickets($id)
    {
        /** @var \app\modules\admin\models\search\Orders $modelOrders */
        $modelOrders = new \app\modules\admin\models\search\Orders();
        $modelOrders = $modelOrders::findOne(['id' => $id]);
        /** @var \app\models\Booking $model */
        $modelBooking = new \app\models\Booking();
        $modelBooking = $modelBooking::findOne(['id' => $modelOrders->id]);
        /** @var \app\models\TicketBookedBuy $modelsTicketBookedBuy */
        $modelsTicketBookedBuy = new \app\models\TicketBookedBuy();
        $modelsTicketBookedBuy = $modelsTicketBookedBuy::findAll(['order_id' => $modelOrders->id]);
        /** @var \app\models\Tour $modelTour */
        $modelTour = new \app\models\Tour();
        $modelTour = $modelTour::findOne(['name' => Yii::$app->tour->getProductNameByOrderId($modelOrders->id)]);
        /** @var \app\models\Events $modelEvent */
        $modelEvent = new \app\models\Events();
        $modelEvent = $modelEvent::findOne(['id' => $modelsTicketBookedBuy[0]->event_id]);

        $content = Yii::$app->controller->renderPartial('//' . Yii::$app->params['templatePdfUrl'] . 'common',[
            'modelTour'    => $modelTour,
            'customer'     => Yii::$app->customer->getCustomerFullNameById($modelOrders->customer_id),
            'modelOrders'  => $modelOrders,
            'modelBooking' => $modelBooking,
            'modelEvent'   => $modelEvent,
        ]);
        foreach ($modelsTicketBookedBuy as $modelTicketBookedBuy){

            /** @var \app\services\Ticket $modelTicket */
            $modelTicket = Yii::$app->ticket->findModelById($modelTicketBookedBuy->ticket_id);
            // get your HTML raw content without any layouts or scripts
            $content .= Yii::$app->controller->renderPartial('//' . Yii::$app->params['templatePdfUrl'] . 'ticket',[
                'modelBooking'         => $modelBooking,
                'modelTicketBookedBuy' => $modelTicketBookedBuy,
                'modelEvent'           => $modelEvent,
                'modelTour'            => $modelTour,
                'modelTicket'          => $modelTicket,
                'totalPrice'           => 100,
                'customer'             => Yii::$app->customer->getCustomerFullNameById($modelOrders->customer_id),
                'linkInfo'             => base64_decode($modelTour->link_info),

            ]);
        }

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_FILE,
            'filename' => (string)(Yii::$app->basePath . '/../web/uploads/' .'ticket(s)-order#' . $modelOrders->id . '.pdf'),
            //'tempPath' => (string)Yii::$app->params['tempPath'],
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => (string)Yii::$app->params['admin.alias']],
        ]);
        // return the pdf output as per the destination setting
        return $pdf->render();
    }

}