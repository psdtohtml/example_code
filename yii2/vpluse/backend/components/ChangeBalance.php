<?php
namespace backend\components;

use Yii;
use yii\base\Behavior;
use common\models\User;
use common\models\rebate\History;
use ErrorException;

class ChangeBalance extends Behavior
{
    /**
     * Change balance
     * @param User $user
     * @param $balance string
     * @param $note string
     * @param $deducted bool
     *
     * @return bool
     */
    public function changeBalance(User $user, $balance, $note, $company = '', $deducted = false)
    {
        $transaction = User::getDb()->beginTransaction();
        try {
            $user->scenario = User::SCENARIO_BALANCE;
            if ($deducted) {
                $user->balance -= $balance;
            } else {
                $user->balance += $balance;
            }
            if($user->balance < 0) {
                throw new ErrorException('Отрицательный баланс');
            }
            $user->save();

            //Сохраняем в журнал
            $param['id_user'] = $user->id;
            $param['from_where'] = $company;
            $param['credit'] = $balance;
            $param['orientation'] = $deducted ? 1 : 0;
            $param['note'] = $note;

            if(!$this->journalSave($param)) {
                throw new ErrorException('Не удалось сделать запись в таблицу "history"');
            }

            if(!$deducted && $user->ref_id) {

                $ref_balance = $balance / 100 * Yii::$app->params['refBonus'];
                $user_ref = User::findOne($user->ref_id);
                if($user_ref) {
                    $user_ref->scenario = User::SCENARIO_BALANCE_PARTNER;
                    $user_ref->balance_partner += $ref_balance;
                    $user_ref->save();
                    //Сохраняем в журнал
                    $param['id_user'] = $user->ref_id;
                    $param['credit'] = $ref_balance;
                    $param['from_where'] = $user->username . '/' .$company;
                    $param['orientation'] = 2;

                    if(!$this->journalSave($param)) {
                        throw new ErrorException('Не удалось сделать запись в таблицу "history"');
                    }
                    $params = [
                        'credit' => $param['credit'],
                        'from_where' => $param['from_where'],
                    ];
                    $user_ref->sendMail(7, $params);
                }
            }

            $transaction->commit();

            return true;
        } catch(ErrorException $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());

            return false;
        }
    }

    /**
     * Change balance
     * @param User $user
     * @param $balance string
     * @param $note string
     * @param $company string
     * @param $deducted bool
     *
     * @return bool
     */
    public function changePartnerBalance(User $user, $balance, $note, $company = '', $deducted = false)
    {
        $transaction = User::getDb()->beginTransaction();
        try {
            $user->scenario = User::SCENARIO_BALANCE_PARTNER;
            if ($deducted) {
                $user->balance_partner -= $balance;
            } else {
                $user->balance_partner += $balance;
            }
            if($user->balance_partner < 0) {
                throw new ErrorException('Отрицательный баланс');
            }
            $user->save();

            //Сохраняем в журнал
            $param['id_user'] = $user->id;
            $param['from_where'] = $company;
            $param['credit'] = $balance;
            $param['orientation'] = 2;
            $param['note'] = $note;

            if(!$this->journalSave($param)) {
                throw new ErrorException('Не удалось сделать запись в таблицу "history"');
            }

            $transaction->commit();

            return true;
        } catch(ErrorException $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());

            return false;
        }
    }

    private function journalSave($param)
    {
        $history = new History();
        $history->id_admin = Yii::$app->user->identity->id;
        $history->id_user = $param['id_user'];
        $history->from_where = $param['from_where'];
        $history->operation_date = date('Y-m-d H:i:s');
        $history->credit = $param['credit'];
        $history->orientation = $param['orientation'];
        $history->note = $param['note'];


        if(!$history->save()) {

            return false;
        }

        return true;
    }
}