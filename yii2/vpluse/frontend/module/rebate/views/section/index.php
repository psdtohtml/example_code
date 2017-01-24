<?php
/* @var $this yii\web\View */
/* @var $model common\models\rebate\Section */
/* @var $companies array */
/* @var $user_requests array */

use yii\helpers\Html;

$this->title = $model->title;
?>

<div class="content__section">
    <div class="info">
        <span class="title-block"><?= $model->title ?></span>
        <div class="info__block">
            <div class="info__title">Внимание!</div>
            <div class="info__text"><?= $model->attention_block ?></div>
        </div>
        <div class="form-send">
            <?= HTML::beginForm() ?>
                <input type="hidden" name="UserRequest[id_user]" value="<?= Yii::$app->user->id ?>">
                <input type="hidden" name="UserRequest[id_section]" value="<?= Yii::$app->request->queryParams['id'] ?>">
                <div class="form-send__box">
                    <select class="js-select" name="UserRequest[id_company]">
                        <option></option>
                        <?php
                        foreach ($companies as $company) {
                            echo '<option value="' . $company->id . '">' . $company->name . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-send__box">
                    <input type="text" class="form-send__invoice" name="UserRequest[account]" placeholder="Дополнительная информация">
                </div>
                <div class="form-send__box form-send__wrap-btn">
                    <button class="form-send__btn btn">Отправить</button>
                </div>
            <?= HTML::endForm() ?>
        </div>
    </div>
    <div class="all-bid">
        <span class="title-block">Все заявки</span>
        <div class="all-bid__table">
            <table class="rtable rtable--flip">
                <thead>
                <tr>
                    <th>Компания</th>
                    <th>Дополнительная информация</th>
                    <th>Статус</th>
                    <th>Дата</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($user_requests) {
                    foreach ($user_requests as $request) {
                        switch ($request->status) {
                            case 0 : $color = ''; break;
                            case 1: $color = 'confirmed'; break;
                            case 2 : $color = 'rejected'; break;
                            default: $color = ''; break;
                        }
                    ?>
                        <tr>
                            <td><?= $request->getCompanyName() ?></td>
                            <td><?= $request->account ?></td>
                            <td class="all-bid__table_<?= $color ?>"><?= $request->getStatusName() ?></td>
                            <td><?= Yii::$app->formatter->asDate($request->created_at) ?></td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="4">Заявок нет</td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>