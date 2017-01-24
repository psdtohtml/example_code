<?php

namespace app\models;

use Yii;
use app\models\data\Tour as BaseTour;
use yii\db\Query;

/**
 * This is the model class for table "tour".
 *
 * @property integer $id
 * @property string $name
 * @property string $recurring
 * @property string $currency
 * @property integer $ticket_available
 * @property integer $ticket_booking_available
 * @property integer $ticket_minimum
 * @property integer $customer_ticket_limit
 * @property string $notice
 * @property string $start_tour_date
 * @property string $start_tour_time
 * @property string $end_tour_date
 * @property string $end_tour_time
 * @property integer $tax_id
 * @property array $logo generated filename on server
 * @property string $filename source filename from client
 * @property string $e_phone
 * @property string $meeting_point
 * @property string $link_info
 * @property string $confirmation_mail
 *
 * @property Variant[] $variants
 *
 * @package app\models
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Tour extends BaseTour
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['recurring', 'start_tour_date', 'end_tour_date', 'start_tour_time', 'end_tour_time', 'name', 'ticket_available'], 'required'],
            [['id', 'ticket_available', 'manager_id'], 'integer'],
            [['start_tour_date', 'start_tour_time', 'end_tour_date', 'end_tour_time', 'name', 'recurring', 'currency'], 'safe'],
            [['name'],'unique'],
            [['link_info'], 'safe'],
            [['confirmation_mail'], 'safe'],
        ];
    }

}
