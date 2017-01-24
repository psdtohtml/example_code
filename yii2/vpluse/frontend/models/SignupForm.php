<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $local_storage;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Это имя пользователя уже занято.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Этот адрес электронной почты уже занят.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['local_storage', 'in', 'range' => ['ok'], 'message' => 'Вы уже зарегистрированы на этом сайте.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'email' => 'Email',
            'password' => 'Пароль',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        $length = 8;
        $char_types = "lower,numbers";
        $ref_code = $this->random_string($length, $char_types);
        while ($this->checkUniqueCode($ref_code)) {
            $ref_code = $this->random_string($length, $char_types);
        }
        $user->ref_code = $ref_code;

        $cookies = Yii::$app->request->cookies;
        if (($cookie = $cookies->get('referral')) !== null) {
            $referral = $cookie->value;
            $referral_user = User::find()->select(['id'])->where(['ref_code' => $referral])->one();
            if($referral_user) {
                $user->ref_id = $referral_user->id;
            }
        }

        return $user->save() ? $user : null;
    }

    private function checkUniqueCode( $ref_code )
    {
        return User::find()
            ->where(['ref_code' => $ref_code])
            ->one();
    }

    private function random_string($length, $chartypes)
    {
        $chartypes_array=explode(",", $chartypes);
        // задаем строки символов.
        //Здесь вы можете редактировать наборы символов при необходимости
        $lower = 'abcdefghijklmnopqrstuvwxyz'; // lowercase
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; // uppercase
        $numbers = '1234567890'; // numbers
        $special = '^@*+-+%()!?'; //special characters
        $chars = "";
        // определяем на основе полученных параметров,
        //из чего будет сгенерирована наша строка.
        if (in_array('all', $chartypes_array)) {
            $chars = $lower . $upper. $numbers . $special;
        } else {
            if(in_array('lower', $chartypes_array))
                $chars = $lower;
            if(in_array('upper', $chartypes_array))
                $chars .= $upper;
            if(in_array('numbers', $chartypes_array))
                $chars .= $numbers;
            if(in_array('special', $chartypes_array))
                $chars .= $special;
        }
        // длина строки с символами
        $chars_length = strlen($chars) - 1;
        // создаем нашу строку,
        //извлекаем из строки $chars символ со случайным
        //номером от 0 до длины самой строки
        $string = $chars{rand(0, $chars_length)};
        // генерируем нашу строку
        for ($i = 1; $i < $length; $i = strlen($string)) {
            // выбираем случайный элемент из строки с допустимыми символами
            $random = $chars{rand(0, $chars_length)};
            // убеждаемся в том, что два символа не будут идти подряд
            if ($random != $string{$i - 1}) $string .= $random;
        }
        // возвращаем результат
        return $string;
    }
}
