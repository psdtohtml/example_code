<?php

namespace backend\models\rebate;

use common\models\User;
use Yii;
use backend\models\rebate\UserGroup;

/**
 * This is the model class for table "group".
 *
 * @property integer $id
 * @property string $name
 */
class Group extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название группы',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function inGroupUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])
            ->viaTable('user_group', ['group_id' => 'id'])
            ->where(['subscription' => 1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function outGroupUsers()
    {
        $subQuery = UserGroup::find()
            ->select ('user_id')
            ->where(['group_id' => $this->id]);

        return User::find()->leftJoin('user_group', 'user.id = user_group.user_id')
            ->where(['not in', 'user.id', $subQuery])
            ->andWhere(['subscription' => 1]);

    }
}
