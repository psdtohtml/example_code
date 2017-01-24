<?php
namespace backend\models\rebate;

use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Password reset form
 */
class ImportForm extends Model
{
    public $csv;
    public $company;
    public $account;
    public $balance;

    const SCENARIO_UPLOAD = 'upload';
    const SCENARIO_VALIDATION = 'validation';


    public function rules()
    {
        return [
            [['csv'],'file','extensions'=>'csv', 'checkExtensionByMimeType' => false, 'skipOnEmpty' => false],
            [['company', 'account', 'balance'],'required'],
            [['company', 'account'],'string', 'length' => [1, 250]],
            ['balance', 'filter', 'filter' => function ($value) {
                $value = str_replace(',', '.', $value);
                return $value;
            }],
            [['balance'],'number'],

        ];
    }

    public function attributeLabels()
    {
        return [
            'csv' => 'Выбирите CSV файл',
            'company' => 'Компания',
            'account' => 'Счет',
            'balance' => 'Сумма',
        ];
    }

    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_VALIDATION => ['company', 'account', 'balance'],
            self::SCENARIO_UPLOAD => ['csv'],
        ]);
    }


}