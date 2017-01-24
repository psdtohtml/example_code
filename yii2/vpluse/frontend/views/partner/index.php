<?php
use yii\helpers\Url;
use frontend\assets\PartnerAsset;

PartnerAsset::register($this);
$ref_code = Yii::$app->user->identity->ref_code;
?>

<div class="content__section">
    <div class="info">
        <span class="title-block">
            Кабинет партнера
        </span>
        <div class="info__block">
            <p class="info__text">
                <p>Ваша реферальная ссылка для привлечения клиентов в Vpluse:</p>
                <p id="partner_link_result"><?= Url::to(['/', 'ref' => $ref_code], true) ?></p>
            </p>
            <br><br><br><br>
            <p>
                <p>Форма генерации ссылки на любую другую страницу сайта:</p>
                <p>Введите любую ссылку сайта vpluse.net на которую должна вести партнерская ссылка</p>
            <br>
                <input id="ref_code" type="hidden" value="<?= $ref_code ?>">
                <input id="home_page" type="hidden" value="<?= Url::to(['/', 'ref' => $ref_code], true) ?>">
                <input id="partner_page" type="text" class="form-control">
            <br>
                <button id="partner_link_generate" type="button" class="btn">Применить</button>
            </p>
            <br><br>

        </div>
    </div>
</div>
