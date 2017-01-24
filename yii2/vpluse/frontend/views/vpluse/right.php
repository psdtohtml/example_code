<?php
use frontend\widgets\NewsWidget;
use frontend\widgets\StaticBlockWidget;
use yii\helpers\Url;
?>
<?php
if($ref_back = StaticBlockWidget::widget(['id' => 1])) {
    ?>
    <div class="refback">
        <?= $ref_back ?>
    </div>
    <?php
}
?>
<div class="news">
    <span class="title-block">Наши новости</span>
    <ul class="news__list">
        <?php NewsWidget::begin(); ?>
        <li class="news__item">
            <div class="news__date">:date</div>
            <a class="news__title" href="<?= Url::to(['/site/news', 'id' => 'newsId']) ?>"><span>:title</span></a>
        </li>
        <?php NewsWidget::end(); ?>
    </ul>
</div>
