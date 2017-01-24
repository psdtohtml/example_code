<?php

namespace app\modules\auth\models\forms;

use app\models\forms\LoginForm as BaseLoginForm;

/**
 * Class LoginForm. Implements default auth model.
 * @package app\modules\auth\models\forms
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class LoginForm extends BaseLoginForm
{
    /** @var  string $email */
    public $email;

    /** @var  string $username */
    public $username;

}
