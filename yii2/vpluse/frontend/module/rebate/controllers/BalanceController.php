<?php
namespace app\module\rebate\controllers;

use Yii;
use yii\web\Controller;
use common\models\rebate\History;
use yii\data\Pagination;

/**
 * ProfileController implements the CRUD actions for User model.
 *
 */
class BalanceController extends Controller
{
    public $layout = 'vpluse';
    private $count_graphic_point = 30;

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = 'vpluse_balance';

        //выполняем запрос
        $query = History::find()->where('id_user=' . Yii::$app->user->id)
            ->andFilterWhere(['!=', 'orientation', 2])
            ->orderBy('operation_date DESC');
        //делаем копию выборки
        $countQuery = $modelQuery  = clone $query;

        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => $this->getPageSize()
        ]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;
        $history = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $model = $modelQuery->all();

        if(Yii::$app->request->isAjax) {
            if($model && $post = Yii::$app->request->post()) {
                if(isset($post['detail'])) {
                    $detail = [1 => 'from_where', 2 => 'note'];
                    $diagramData = $this->diagramData($model, $detail[$post['detail']]);

                    exit($diagramData);
                }
                if(isset($post['graphic_detail'])) {
                    $page = isset($post['graphic_page']) ? $post['graphic_page'] : 0;
                    $start_date= isset($post['start_date']) ? $post['start_date'] : 0;
                    $end_date = isset($post['end_date']) ? $post['end_date'] : 0;
                    $graphicData = $this->graphicData($post['graphic_detail'], $page, $start_date, $end_date);

                    exit($graphicData);
                }
            }
            exit(json_encode([]));
        }

        return $this->render('index', [
            'history' => $history,
            'pages' => $pages,
        ]);
    }

    private function getDataInfo($mysqlData)
    {
        $timestamp_start_data  = strtotime($mysqlData);
        $start_data_info = getdate ($timestamp_start_data);

        return $start_data_info;

    }

    private function graphicData($detail = 'month', $page = 0, $start_date = 0, $end_date = 0)
    {
        $limit = $this->count_graphic_point;
        $offset = $limit * $page;
        $graphicData = [];
        $filter_where_start = ['>', 'id', 0];
        $filter_where_end = ['>', 'id', 0];
        if($start_date) {
            $filter_where_start = ['>', 'operation_date', $start_date];
        }
        if($end_date) {
            $filter_where_end = ['<=', 'operation_date', $end_date];
        }
        $month_names = ['','Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
            'Июль', 'Август', 'Cентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
        $month_names_rp = ['','Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня',
            'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];
        switch ($detail) {
            case 'month':
                $history = History::find()
                    ->select(['operation_date', 'SUM(credit) as sum'])
                    ->where([
                        'id_user'=> Yii::$app->user->id,
                        'orientation' =>0,
                    ])
                    ->andWhere($filter_where_start)
                    ->andWhere($filter_where_end)
                    ->groupBy('YEAR(operation_date), MONTH(operation_date)')
                    ->limit($limit)
                    ->offset($offset)
                    ->orderBy('operation_date DESC')
                    ->all();
                $history = array_reverse($history);
                foreach ($history as  $key => $item) {
                    $year = $this->getDataInfo($item->operation_date)['year'];
                    $month = $this->getDataInfo($item->operation_date)['mon'];
                    $graphicData['cat'][$key] = $month_names[$month] .
                        '(' . $year . ')';
                    $graphicData['val'][$key] = (float)$item->sum;
                }
                break;
            case 'day':
                $history = History::find()
                    ->select(['operation_date', 'SUM(credit) as sum'])
                    ->where(['id_user'=> Yii::$app->user->id, 'orientation' =>0])
                    ->andWhere($filter_where_start)
                    ->andWhere($filter_where_end)
                    ->groupBy('YEAR(operation_date), MONTH(operation_date), DAY(operation_date)')
                    ->limit($limit)
                    ->offset($offset)
                    ->orderBy('operation_date DESC')
                    ->all();
                $history = array_reverse($history);
                foreach ($history as  $key => $item) {
                    $day = $this->getDataInfo($item->operation_date)['mday'];
                    $month = $this->getDataInfo($item->operation_date)['mon'];
                    $graphicData['cat'][$key] = $day .
                        ' ' . $month_names_rp[$month];
                    $graphicData['val'][$key] = (float)$item->sum;
                }
                break;
            case 'week':
                $history = History::find()
                    ->select(['operation_date', 'SUM(credit) as sum'])
                    ->where(['id_user'=> Yii::$app->user->id, 'orientation' =>0])
                    ->andWhere($filter_where_start)
                    ->andWhere($filter_where_end)
                    ->groupBy('YEAR(operation_date), MONTH(operation_date), WEEK(operation_date)')
                    ->limit($limit)
                    ->offset($offset)
                    ->orderBy('operation_date DESC')
                    ->all();
                $history = array_reverse($history);
                foreach ($history as  $key => $item) {
                    $date = $this->getDataInfo($item->operation_date)[0];
                    $week_day = $this->getDataInfo($item->operation_date)['wday'];
                    $graphicData['cat'][$key] = $this->getWeekInterval($week_day, $date);
                    $graphicData['val'][$key] = (float)$item->sum;
                }
                break;
        }

        return json_encode($graphicData);
    }

    private function diagramData($history, $detail = 'from_where')
    {
        $diagramData = [];
        foreach ($history as $item) {
            if ($item->orientation != 0) {
                continue;
            }
            if(isset($diagramData[$item->$detail])) {
                $diagramData[$item->$detail] += $item->credit;
            } else {
                $diagramData[$item->$detail] = $item->credit;
            }
        }
        $i = 0; $diagram_data = [];
        foreach ($diagramData as $key => $value) {
            $diagram_data[$i]['name'] = $key;
            $diagram_data[$i]['y'] = (int)$value;
            $i++;
        }

        return json_encode($diagram_data);
    }

    private function getWeekInterval($week_day, $date_timestamp) {
        $day_timestamp = 24*3600;
        $week_interval = '';
        $format_date = 'd.m.y';
        switch ($week_day) {
            case 0:
                $week_end_timestamp = $date_timestamp + (6*$day_timestamp);
                $week_interval =  date($format_date, $date_timestamp) . '-' . date($format_date, $week_end_timestamp);
                break;
            case 1:
                $week_start_timestamp = $date_timestamp - ($day_timestamp);
                $week_end_timestamp = $date_timestamp + (5*$day_timestamp);
                $week_interval =  date($format_date, $week_start_timestamp) . '-' . date($format_date, $week_end_timestamp);
                break;
            case 2:
                $week_start_timestamp = $date_timestamp - (2*$day_timestamp);
                $week_end_timestamp = $date_timestamp + (4*$day_timestamp);
                $week_interval =  date($format_date, $week_start_timestamp) . '-' . date($format_date, $week_end_timestamp);
                break;
            case 3:
                $week_start_timestamp = $date_timestamp - (3*$day_timestamp);
                $week_end_timestamp = $date_timestamp + (3*$day_timestamp);
                $week_interval =  date($format_date, $week_start_timestamp) . '-' . date($format_date, $week_end_timestamp);
                break;
            case 4:
                $week_start_timestamp = $date_timestamp - (4*$day_timestamp);
                $week_end_timestamp = $date_timestamp + (2*$day_timestamp);
                $week_interval =  date($format_date, $week_start_timestamp) . '-' . date($format_date, $week_end_timestamp);
                break;
            case 5:
                $week_start_timestamp = $date_timestamp - (5*$day_timestamp);
                $week_end_timestamp = $date_timestamp + (1*$day_timestamp);
                $week_interval =  date($format_date, $week_start_timestamp) . '-' . date($format_date, $week_end_timestamp);
                break;
            case 6:
                $week_start_timestamp = $date_timestamp - (6*$day_timestamp);
                $week_interval =  date($format_date, $week_start_timestamp) . '-' . date($format_date, $date_timestamp);
                break;
        }

        return $week_interval;
    }

    private function getPageSize()
    {
        $pageSizeDefault = 20;
        if (!isset(Yii::$app->request->queryParams['limit'])) {
            $pageSize = $pageSizeDefault;
        } elseif (Yii::$app->request->queryParams['limit'] == 'all') {
            $pageSize = 100000;
        } else {
            $pageSize = Yii::$app->request->queryParams['limit'];

        }
        return $pageSize;
    }
}
