<?php
use yii\helpers\Url;
/* @var $history array */
?>
<div class="content__bottom">
    <div class="history">
        <span class="title-block">история операций</span>
        <div class="history__table">
            <table class="rtable rtable--flip">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>Направление</th>
                    <th>Куда/Откуда</th>
                    <th>Сумма</th>
                    <th>Примечание</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($history) {
                    foreach ($history as $item) {
                        ?>
                        <tr>
                            <td><?= Yii::$app->formatter->asDate($item->operation_date) ?></td>
                            <td class="all-bid__table_confirmed"><?= $item->orientationName ?></td>
                            <td><?= $item->from_where ?></td>
                            <td><?= $item->credit ?></td>
                            <td><?= $item->note ?></td>
                        </tr>
                        <?php
                    }

                } else {
                    ?>
                    <tr>
                        <td char="5">Начислений нет</td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
