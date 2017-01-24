<?php

namespace app\base;

use Yii;
use yii\base\Exception;

/**
 * Class ActiveRecord
 * @package app\base
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class ActiveRecord extends \yii\db\ActiveRecord
{

    /** @var \yii\db\Query $query */
    protected $query;

    /** @var \yii\data\Sort */
    public $sort;

    /**
     * Port from yii 1.
     * @param null $names
     */
    public function unsetAttributes($names = null)
    {
        if ($names === null){
            $names = $this->attributes();
        }
        foreach ($names as $name) {
            $this->$name =null;
        }

    }

    /**
     * @return string model errors as string
     */
    public function getErrorsString()
    {
        $result = '';

        foreach ($this->getErrors() as $attribute => $errors) {
            foreach ($errors as $error) {
                $result .= "{$attribute}: {$error}\n";
            }
        }

        return $result;
    }

    /**
     * Checks if model attribute was changed
     * @param string $attribute Name of the attribute
     * @return bool Is property changed
     */
    public function isChanged($attribute)
    {
        return !isset($this->oldAttributes[$attribute])
            || ($this->oldAttributes[$attribute] != $this->attributes[$attribute]);

    }

}
