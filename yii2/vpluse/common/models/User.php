<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\base\InvalidParamException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;
use common\models\rebate\UserRequest;
use common\models\rebate\History;
use backend\models\EmailTemplates;
use backend\models\Configure;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $avatar
 * @property string $first_name
 * @property string $last_name
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property string $password write-only password
 * @property string $current_pas
 * @property string $new_pas
 * @property string $new_pas_repeat
 * @property string $subscription
 * @property string $ref_id
 * @property string $ref_code
 * @property float $balance
 * @property float $balance_partner
 */
class User extends ActiveRecord implements IdentityInterface
{
    public $current_pas;
    public $new_pas;
    public $new_pas_repeat;

    const SCENARIO_PROFILE = 'profile';
    const SCENARIO_BALANCE = 'balance';
    const SCENARIO_BALANCE_PARTNER = 'balance_partner';
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => time(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['avatar', 'trim'],
            ['first_name', 'trim'],
            ['last_name', 'trim'],

            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],

            [['avatar'], 'file', 'extensions' => 'png, jpg, gif'],
            [['first_name', 'last_name', 'current_pas'], 'string', 'max' => 250],

            [['new_pas'], 'compare'],
            [['balance', 'balance_partner'], 'number'],
            [['ref_id, ref_code'], 'safe'],


        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fullName' => 'Имя Фамилия',
            'username' => 'Логин',
            'balance' => 'Балланс($)',
            'balance_partner' => 'Партнерский балланс($)',
            'subscription' => 'Подписка на новости',
            'subscriptionStatus' => 'Подписка на новости',
            'avatar' => 'Аватар',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'created_at' => 'Дата регистрации',
            'ref_id' => 'Реферал',
            'ref_code' => 'Реферальный код',
        ];
    }

    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_PROFILE => ['avatar', 'first_name', 'last_name', 'subscription'],
            self::SCENARIO_BALANCE => ['balance'],
            self::SCENARIO_BALANCE_PARTNER => ['balance_partner'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        try {
            return Yii::$app->security->validatePassword($password, $this->password_hash);

        } catch (InvalidParamException $e) {
            $this->addError('Неверный формат');
        }

    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return integer
     */
    public function getCountUserRequests()
    {
        return $this->hasMany(UserRequest::className(), ['id_user' => 'id'])->where(['status' => 1])->count();
    }

    /**
     * @return float
     */
    public function getPartnerBalance()
    {
        return $this->hasMany(History::className(), ['id_user' => 'id'])->where(['orientation' => 0])->sum('credit') * 0.07;
    }


    /**
     * Get avatar image
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar ? $this->avatar : Yii::$app->params['noPhoto'];
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $this->deleteAvatar();

            return true;
        } else {
            
            return false;
        }
    }

    public function deleteAvatar($avatar = null)
    {

        if(!$avatar) {
            $avatar = $this->avatar;
        }

        if(!$avatar) {
            return false;
        }

        $file = Yii::getAlias("@webroot") . '/uploads/avatar/' . $avatar;
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /* Геттер для полного имени человека */
    public function getFullName()
    {

        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function subscribers()
    {
        return User::find()->where(['subscription' => 1]);
    }

    /**
     * @param integer $temp_id id of template
     * @param array $params
     * @return bool
     */
    public function sendMail($temp_id, $params = [])
    {
        $template = EmailTemplates::findOne($temp_id);
        if(is_null($template)) {
            return false;
        }
        $message = Yii::$app->mailer->compose();
        if ($params) {
            foreach ($params as $key => $param) {
                $template->from = str_replace('#'.$key.'#', $param, $template->from);
                $template->subject = str_replace('#'.$key.'#', $param, $template->subject);
                $template->body_html = str_replace('#'.$key.'#', $param, $template->body_html);
                $template->body_text = str_replace('#'.$key.'#', $param, $template->body_text);
            }
        }
        $template->from = str_replace('#userName#', $this->fullName, $template->from);
        $template->subject = str_replace('#userName#', $this->fullName, $template->subject);
        $template->body_html = str_replace('#userName#', $this->fullName, $template->body_html);
        $template->body_text = str_replace('#userName#', $this->fullName, $template->body_text);
        $message->setFrom($template->from)
            ->setTo([$this->email => $this->fullName])
            ->setSubject($template->subject)
            ->setHtmlBody($template->body_html)
            ->setTextBody($template->body_text);

        return $message->send();

    }

    /**
     * @param string $view
     * @param string $subject
     * @param array $params
     * @return bool
     */
    public function sendMailFileTemp($view, $subject, $params = []) {
        // Set layout params
        Yii::$app->mailer->getView()->params['userName'] = $this->fullName;

        $result = Yii::$app->mailer->compose([
            'html' => 'views/' . $view . '-html',
            'text' => 'views/' . $view . '-text',
        ], $params)
            ->setTo([$this->email => $this->fullName])
            ->setSubject($subject)
            ->send();

        // Reset layout params
        Yii::$app->mailer->getView()->params['userName'] = null;

        return $result;
    }

    /**
     * @param integer $temp_id
     * @param array $params
     * @return bool
     */
    public static function sendMailToAdmin($temp_id, $params = [])
    {

        $admin_email = Configure::find()->where(['key' => 'admin_email'])->one();

        if(!isset($admin_email->value)) {
            return false;
        }
        $template = EmailTemplates::findOne($temp_id);
        if(is_null($template)) {
            return false;
        }
        $message = Yii::$app->mailer->compose();
        if ($params) {
            foreach ($params as $key => $param) {
                $template->from = str_replace('#'.$key.'#', $param, $template->from);
                $template->subject = str_replace('#'.$key.'#', $param, $template->subject);
                $template->body_html = str_replace('#'.$key.'#', $param, $template->body_html);
                $template->body_text = str_replace('#'.$key.'#', $param, $template->body_text);
            }

        }
        $template->from = str_replace('#userName#', Yii::$app->user->identity->fullName, $template->from);
        $template->subject = str_replace('#userName#', Yii::$app->user->identity->fullName, $template->subject);
        $template->body_html = str_replace('#userName#', Yii::$app->user->identity->fullName, $template->body_html);
        $template->body_text = str_replace('#userName#', Yii::$app->user->identity->fullName, $template->body_text);
        $message->setFrom($template->from)
            ->setTo($admin_email->value)
            ->setSubject($template->subject)
            ->setHtmlBody($template->body_html)
            ->setTextBody($template->body_text);

        return $message->send();

    }

    /**
     * @param integer $temp_id
     * @param array $params
     * @return bool
     */
    public static function sendMails($id_users, $temp_id, $params = [])
    {
        $users = self::find()->where(['IN', 'id', $id_users])->all();
        $template = EmailTemplates::findOne($temp_id);
        if ($params) {
            foreach ($params as $key => $param) {
                $template->body_html = str_replace('#'.$key.'#', $param, $template->body_html);
                $template->body_text = str_replace('#'.$key.'#', $param, $template->body_text);
                $template->from = str_replace('#'.$key.'#', $param, $template->from);
                $template->subject = str_replace('#'.$key.'#', $param, $template->subject);
            }
        }
        $messages = [];
        foreach ($users as $user) {
            $template->from = str_replace('#userName#', $user->fullName, $template->from);
            $template->subject = str_replace('#userName#', $user->fullName, $template->subject);
            $template->body_html = str_replace('#userName#', $user->fullName, $template->body_html);
            $template->body_text = str_replace('#userName#', $user->fullName, $template->body_text);
            $messages[] = Yii::$app->mailer->compose()
                ->setTo([$user->email => $user->fullName])
                ->setSubject($template->subject)
                ->setHtmlBody($template->body_html)
                ->setTextBody($template->body_text);
        }

        return Yii::$app->mailer->sendMultiple($messages);

    }

    public function getSubscriptionStatus() {

        if($this->subscription) {

            return 'Да';
        }

        return 'Нет';
    }
}
