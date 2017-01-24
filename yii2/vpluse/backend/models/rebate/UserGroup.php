<?php

namespace backend\models\rebate;

use Yii;
use common\models\User;

/**
 * This is the model class for table "user_group".
 *
 * @property integer $user_id
 * @property integer $group_id
 */
class UserGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'group_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fullName' => 'Имя Фамилия',
            'login' => 'Логин',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    /* Геттер для логина*/
    public function getLogin()
    {
        $username = '';
        if($this->user) {
            $username = $this->user->username;
        }

        return $username;
    }
    /* Геттер для полного имени человека */
    public function getFullName()
    {
        $full_name = '';
        if($this->user) {
            $full_name = $this->user->getFullName();
        }

        return $full_name;

    }

}
