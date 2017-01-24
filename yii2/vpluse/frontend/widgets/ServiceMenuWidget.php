<?php

namespace frontend\widgets;

use yii\base\Widget;
use common\models\Service;
use yii\helpers\Url;

/*
 * NewsWidget::begin();
 * <p>:date</p>
 * <h1>:title</h1>
 * NewsWidget::end();
 */
class ServiceMenuWidget  extends Widget
{
    public $menu_services = [];


    public function init()
    {
        parent::init();

        $this->menu_services = Service::find()->where('status=1')->all();
        $partner = ['slug' => 'partner', 'menu_item' => 'Кабинет партнера'];
        array_push($this->menu_services, $partner);
    }

    public function run()
    {
        if ($this->menu_services) {
            $current_service = isset($_SESSION['service']) ? $_SESSION['service'] : null;
            foreach ($this->menu_services as $service ) {
                $item_active = '';
                if ($service['slug'] == $current_service) {
                    $item_active = ' nav-block__item-active';
                }
                $href = Url::to(['/', 'service' => $service['slug']]);
                if ($service['slug'] == 'partner') {
                    $href = Url::to(['/partner']);
                }
                echo '<a class="nav-block__item' . $item_active . '" href="' . $href . '">';
                echo '<span class="nav-block__text">' . $service['menu_item'] . '</span>';
                echo '</a>';
            }
        }
    }
}