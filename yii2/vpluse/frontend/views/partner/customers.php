<?php
/* @var $service string */
/* @var $customers array */
/* @var $customer \common\models\User */
?>
<div class="content__bottom">
    <div class="history">
        <span class="title-block">Клиенты</span>
        <div class="history__table">
            <table class="rtable rtable--flip">
                <thead>
                <tr>
                    <th>Клиент</th>
                    <th>Регистрация</th>
                    <th>Счета</th>
                    <th>Доход</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($customers) {

                    foreach ($customers as $customer) {

                        ?>
                        <tr>
                            <td><?= $customer->username ?></td>
                            <td><?= Yii::$app->formatter->asDate($customer->created_at) ?></td>
                            <td><?= $customer->countUserRequests ?></td>
                            <td><?= $customer->partnerBalance ?></td>
                        </tr>
                        <?php
                    }

                } else {
                    ?>
                    <tr>
                        <td char="4">Клиентов нет</td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
