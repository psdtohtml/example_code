<?php

namespace frontend\widgets;

use yii\base\Widget;
use common\models\rebate\BaseSection;
use common\models\rebate\Section;
use yii\helpers\Url;

/*
 * NewsWidget::begin();
 * <p>:date</p>
 * <h1>:title</h1>
 * NewsWidget::end();
 */
class SectionMenuWidget  extends Widget
{
    public $menu_sections = [];


    public function init()
    {
        parent::init();

        $base_sections = BaseSection::find()->all();
        $sections = Section::find()->all();
        if ($base_sections) {
            foreach ($base_sections as $base_section) {
                if ($sections) {
                    foreach ($sections as $section) {
                        if ($section['id_base'] == $base_section['id']) {
                            $this->menu_sections[$base_section['name']][] = ['title' => $section['title'], 'id' => $section['id']];
                        }

                    }
                }
            }
        }
    }

    public function run()
    {

        if ($this->menu_sections) {
            $icons = ['item-trader', 'item-investor', 'item-webmaster'];
            $i = 0;
            foreach ($this->menu_sections as $base => $sections ) {
                echo '<li class="sidebar__row">';
                echo '<a class="sidebar__item sidebar__' . $icons[$i] . ' js-drop" href="#?">';
                echo '<span class="sidebar__title">' . $base . '</span>';
                echo '</a>';
                if ($sections) {
                    echo '<ul class="dropdown">';
                    foreach ($sections as $section) {
                        echo '<li class="dropdown__row">';
                        echo '<a class="dropdown__item" href="' . Url::to(['/rebate/section', 'id' => $section['id']]) . '">' . $section['title'] . '</a>';
                        echo '</li>';
                    }
                    echo '</ul>';
                }
                $i++;
            }
        }
    }
}