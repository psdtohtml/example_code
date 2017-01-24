<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%email_templates}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $from
 * @property string $subject
 * @property string $body_html
 * @property string $body_text
 * @property string $var
 */
class EmailTemplates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%email_templates}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'from', 'subject', 'body_html', 'body_text'], 'required'],
            [['body_html', 'body_text', 'var'], 'string'],
            [['name', 'from', 'subject'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название шаблона',
            'from' => 'Адрес отправитля',
            'subject' => 'Тема',
            'body_html' => 'Формат Html',
            'body_text' => 'Формат Text',
        ];
    }
}
