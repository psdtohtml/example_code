<?php

namespace app\models\data;

use Yii;

/**
 * This is the model class for table "answer".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $question_id
 * @property string $answer
 *
 * @property Orders $order
 *
 * @package app\models\data
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Answer extends \app\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'answer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'question_id'], 'integer'],
            [['answer'], 'string', 'max' => 255],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'question_id' => 'Question ID',
            'answer' => 'Answer',
        ];
    }

}
