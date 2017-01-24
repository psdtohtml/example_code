<?php
namespace frontend\widgets;

use Yii;
use yii\base\Widget;
use common\models\rebate\News;

/*
 * NewsWidget::begin();
 * <p>:date</p>
 * <h1>:title</h1>
 * NewsWidget::end();
 */
class NewsWidget  extends Widget
{
    public $news_block_html = '';
    public $count_symbols = 200;

    public function init()
    {
        parent::init();

        ob_start();
    }

    public function run()
    {
        $temp = ob_get_clean();
        $ar_news = News::find()->orderBy('created_at')->limit(3)->all();
        if($ar_news) {
            foreach ($ar_news as $news) {
                $content = str_replace(':date', Yii::$app->formatter->asDate($news->created_at), $temp);
                $content = str_replace(':title', $news->title, $content);
                $content = str_replace('newsId', $news->id, $content);
                //$content = str_replace(':text', substr($news->content, 0, $this->count_symbols) . '...', $content);
                $this->news_block_html .= $content;
            }
        }

        return $this->news_block_html;
    }
}