<?php
namespace backend\widgets;

use Yii;
use yii\base\Widget;
use yii\grid\GridView;

class CustomGridWidget extends Widget
{
    private $pageSizeDefault = 20;
    private $counts_pages = [20, 50, 100, 200, 500];
    private $current_count_pages;
    public $dataProvider;
    public $searchModel;
    public $columns;

    public function init()
    {
        parent::init();
        if (!isset(Yii::$app->request->queryParams['limit'])) {
            $this->dataProvider->setPagination([
                'pageSize' => $this->pageSizeDefault
            ]);
        } elseif (Yii::$app->request->queryParams['limit'] == 'all') {
            $this->dataProvider->setPagination(false);
        } else {
            $this->dataProvider->setPagination([
                'pageSize' => Yii::$app->request->queryParams['limit']
            ]);
        }
        $this->current_count_pages = isset(Yii::$app->request->queryParams['limit']) ? Yii::$app->request->queryParams['limit'] : $this->pageSizeDefault;

    }

    public function run()
    {
        $html = '';
        foreach ($this->counts_pages as $button) {
            $html .= '
            <a href="?limit=' . $button . '">
                <span class="badge bg-' . ($this->current_count_pages == $button ? 'green': 'light-blue') . '">
                    ' . $button . '
                </span>
            </a>';
        }
        $html .= '
            <a href="?limit=all">
                <span class="badge bg-' . ($this->current_count_pages == 'all' ? 'green': 'light-blue') . '">
                    Все
                </span>
            </a>';

        return $html . GridView::widget([
            'pager' => [
                'firstPageLabel' => 'Первая страница',
                'lastPageLabel'  => 'Последняя страница'
            ],
            'dataProvider' => $this->dataProvider,
            'filterModel' => $this->searchModel,
            'summary' => 'Показано {count} из {totalCount}',
            'columns' => $this->columns
        ]);
    }
}