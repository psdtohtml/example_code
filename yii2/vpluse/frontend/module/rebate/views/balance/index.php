<?php
use frontend\assets\GraphicsAsset;
use yii\widgets\LinkPager;
use yii\bootstrap\ButtonDropdown;

/* @var $this yii\web\View */
/* @var $history common\models\rebate\History */

$this->title = 'История операций';
GraphicsAsset::register($this);
?>
<style>
    * {
        box-sizing: border-box;
    }
</style>
<div class="content__main">
    <div class="content__section">
        <div class="profit">
            <span class="title-block">Начисления</span>
            <div class="sort">
                <div class="sort__box sort__switch">
                    <a id="graphic_prev" class="sort__control sort__control_prev" href="javascript:void(0)"></a>
                    <a id="graphic_next" class="sort__control sort__control_next" href="javascript:void(0)"></a>
                </div>
                <div class="sort__box">
                    <input class="form-send__invoice" type="text" name="daterange" value="" placeholder="Период" />
                </div>
                <!--<div class="sort__box">
                    <select class="js-select">
                        <option>31.06 - 30-10</option>
                        <option>31.10 - 30-12</option>
                        <option>31.12 - 01-01</option>
                    </select>
                </div>-->
                <div class="sort__box">
                    <select  id="graphic_select" class="js-select">
                        <option value="month">Детализация по месяцам</option>
                        <option value="day" selected>Детализация по дням</option>
                        <option value="week">Детализация по неделям</option>
                    </select>
                </div>
            </div>
            <div class="profit__diagram">
                <div id="container_graph" style="height: 300px"></div>
                <!--<img src="/img/diagram_1.png" alt="diagram">-->
            </div>
        </div>
    </div>
</div>

<div class="content__addit center-box">
    <div class="specification">
        <span class="title-block">Детализация</span>
        <div class="sort">
            <div class="sort__box">
                <select id="detail_select" class="js-select">
                    <option value="1">Детализация по компаниям</option>
                    <option value="2">Детализация по счетам</option>
                </select>
            </div>
        </div>
        <div class="specification__diagram">
            <div id="container_diagram" style="height: 300px"></div>
            <!--<img src="/img/diagram_2.png" alt="diagram">-->
        </div>
    </div>
</div>


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
                        $color = $item->orientation == 0 ? 'confirmed' : 'rejected';
                        ?>
                        <tr>
                            <td><?= Yii::$app->formatter->asDate($item->operation_date) ?></td>
                            <td class="all-bid__table_<?= $color ?>"><?= $item->orientationName ?></td>
                            <td><?= $item->from_where ?></td>
                            <td><?= $item->credit ?></td>
                            <td><?= $item->note ?></td>
                        </tr>
                        <?php
                    }

                } else {
                    ?>
                    <tr>
                        <td char="5">Операций нет</td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php
            // отображаем постраничную разбивку
            echo LinkPager::widget([
                'pagination' => $pages,
                'firstPageLabel' => 'Первая',
                'lastPageLabel' => 'Последняя'
            ]);
            $pageSizeLimit = $pages->getpageSize();
            $label = $pageSizeLimit == 100000 ? 'Все' : $pageSizeLimit;
            echo ButtonDropdown::widget([
                'containerOptions' => ['class' => 'button_drop_down'],
                'label' => $label,
                'options' => [
                    'class' => 'btn-lg btn-default',
                ],
                'dropdown' => [
                    'items' => [
                        [
                            'label' => '20',
                            'url' => '?limit=20'
                        ],
                        [
                            'label' => '50',
                            'url' => '?limit=50'
                        ],
                        [
                            'label' => '100',
                            'url' => '?limit=100'
                        ],
                        [
                            'label' => '200',
                            'url' => '?limit=200'
                        ],
                        [
                            'label' => '500',
                            'url' => '?limit=500'
                        ],
                        [
                            'label' => 'Все',
                            'url' => '?limit=all'
                        ],
                    ]
                ]
            ]);
            ?>
            <div class="button_drop_down btn-group">
                <span>количество записей</span>
            </div>

        </div>
    </div>
</div>