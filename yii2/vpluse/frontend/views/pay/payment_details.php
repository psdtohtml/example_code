<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $payments_details array */

$this->title = 'Платежные реквизиты';
?>

<div class="content__section">
    <div class="info">
        <span class="title-block"><?= Html::encode($this->title) ?></span>
        <div class="info__block">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th>Платежная система</th>
                    <th>Реквизиты</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($payments_details as $payment) {
                ?>
                    <tr>
                        <td><?= $payment['name'] ?></td>
                        <td><?= $payment['value'] ?></td>
                        <td><?= Html::a('Добавить/Изменить', [Url::to(['/pay/change-payment-detail']), 'pay_id' => $payment['id']], ['class' => 'btn btn-primary']) ?></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

