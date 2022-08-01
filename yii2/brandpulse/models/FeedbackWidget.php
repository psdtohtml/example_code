<?php

namespace common\models;

use common\helpers\LogHelper;
use common\models\query\FeedbackLinkQuery;
use common\models\query\FeedbackProjectQuery;
use common\models\query\FeedbackResultQuery;
use common\models\query\FeedbackWidgetQuery;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use common\helpers\UserHelper;
use yii\helpers\Json;
use common\components\UploadFileBehavior;

/**
 * This is the model class for table "{{%feedback_widget}}".
 *
 * @property int              $id
 * @property int              $feedback_project_id
 * @property string           $name                                                  Название
 * @property int              $type                                                  Типы(1-отзывы,2-опросник)
 * @property string|null      $button_color_bg                                       Цвет фона кнопки виджета
 * @property string|null      $button_color_text                                     Цвет текста виджета
 * @property string|null      $button_text                                           Текст нопки виджета
 * @property int              $status                                                Статус(0-отключен,1-включен)
 * @property string|null      $title                                                 Заголовок блока виджета
 * @property int|null         $option_time_open                                      Через сколько сек открывать
 * @property int              $option_is_auto_open_window                            Включить автоматическое открытие окошка виджета при загрузке страницы
 * @property int              $option_is_close_button                                Отключить вывод кнопки на сайте
 * @property string|null      $option_slug                                           Раздел целевого сайта где показывать виджет
 * @property string|null      $option_geo                                            ГЕО позиционирование
 * @property string|null      $option_type_device                                    Тип устройства
 * @property string|null      $link_url_1                                            Ссылка-1
 * @property string|null      $link_url_2                                            Ссылка-2
 * @property string|null      $link_url_3                                            Ссылка-3
 * @property string|null      $link_url_4                                            Ссылка-4
 * @property string|null      $link_url_5                                            Ссылка-5
 * @property string|null      $link_url_6                                            Ссылка-6
 * @property string|null      $link_name_1                                           Название ссылки-1
 * @property string|null      $link_name_2                                           Название ссылки-2
 * @property string|null      $link_name_3                                           Название ссылки-3
 * @property string|null      $link_name_4                                           Название ссылки-4
 * @property string|null      $link_name_5                                           Название ссылки-5
 * @property string|null      $link_name_6                                           Название ссылки-6
 * @property string|null      $link_logo_1                                           Лого ссылки-1
 * @property string|null      $link_logo_2                                           Лого ссылки-2
 * @property string|null      $link_logo_3                                           Лого ссылки-3
 * @property string|null      $link_logo_4                                           Лого ссылки-4
 * @property string|null      $link_logo_5                                           Лого ссылки-5
 * @property string|null      $link_logo_6                                           Лого ссылки-6
 * @property string|null      $hash                                                  hash для генерации отдельной страницы
 * @property int|null         $is_generate_target_link                               Сгенерировать прямую ссылку на страницу с виджетом
 * @property string           $text_for_negative                                     Текст для тех, кто оставил негативную оценку
 * @property string           $text_for_positive                                     Текст для тех, кто оставил позитивную оценку
 * @property string           $bg_color_target_page                                  цвет фона отдельной страницы
 * @property string           $created_at
 * @property string|null      $updated_at
 *
 * @property FeedbackLink[]   $feedbackLinks
 * @property FeedbackResult[] $feedbackResults
 * @property FeedbackProject  $feedbackProject
 */
class FeedbackWidget extends \yii\db\ActiveRecord
{
    const STATUS_STOPED  = 0; // Приостановлены
    const STATUS_ACTIVED = 1; // Активен

    // Типы виджетов
    const TYPE_REVIEW = 1; // Отзывы

    public static $TYPE_LIST = [
        self::TYPE_REVIEW => 'Сбор отзывов'
    ];

    public static $STATUS_LIST = [
        self::STATUS_STOPED  => 'Приостановлен',
        self::STATUS_ACTIVED => 'Активен',
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%feedback_widget}}';
    }


    public function behaviors()
    {
        return [
            [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                // если вместо метки времени UNIX используется datetime:
                'value'      => new Expression('NOW()'),
            ],
            'uploadFileBehavior' => [
                'class'      => UploadFileBehavior::className(),
                'attributes' => ['link_logo_1'],
                'path'       => Yii::$app->params['uploadFolder'] . Yii::$app->params['uploadFolderFeedback']
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['feedback_project_id', 'name', 'text_for_negative'], 'required'],
            [['feedback_project_id', 'type', 'status', 'option_time_open'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'title', 'option_geo', 'option_type_device'], 'string', 'max' => 500],
            [['button_color_bg', 'button_color_text', 'button_text', 'option_slug'], 'string', 'max' => 255],
            [['feedback_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => FeedbackProject::className(), 'targetAttribute' => ['feedback_project_id' => 'id']],
            [['link_url_1', 'link_url_2', 'link_url_3', 'link_url_4', 'link_url_5', 'link_url_6'], 'string', 'max' => 500],
            [['link_url_1', 'link_url_2', 'link_url_3', 'link_url_4', 'link_url_5', 'link_url_6'], 'url', 'defaultScheme' => 'http'],
            [['link_name_1', 'link_name_2', 'link_name_3', 'link_name_4', 'link_name_5', 'link_name_6'], 'string', 'max' => 255],
            [
                ['link_logo_1', 'link_logo_2', 'link_logo_3', 'link_logo_4', 'link_logo_5', 'link_logo_6'],
                'file',
                'skipOnEmpty' => true,
                'extensions'  => ['png', 'jpg', 'gif'],
                'maxSize'     => 1024 * 1024
            ], // размер файла должен быть меньше 1MB
            [['text_for_negative', 'text_for_positive'], 'string'],
            [['option_is_auto_open_window', 'option_is_close_button', 'is_generate_target_link'], 'boolean'],
            [['hash'], 'string', 'max' => 255],
            [['bg_color_target_page'], 'string', 'max' => 10],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                         => Yii::t('app', 'ID'),
            'feedback_project_id'        => Yii::t('app', 'Feedback Project ID'),
            'name'                       => Yii::t('app', 'Название'),
            'type'                       => Yii::t('app', 'Тип виджета'),
            'button_color_bg'            => Yii::t('app', 'Цвет фона кнопки виджета'),
            'button_color_text'          => Yii::t('app', 'Цвет текста виджета'),
            'button_text'                => Yii::t('app', 'Текст кнопки виджета'),
            'status'                     => Yii::t('app', 'Статус'),
            'title'                      => Yii::t('app', 'Текст предложения'),
            'option_time_open'           => Yii::t('app', 'Через сколько секунд открывать:'),
            'option_slug'                => Yii::t('app', 'Раздел целевого сайта где показывать виджет'),
            'option_geo'                 => Yii::t('app', 'ГЕО позиционирование'),
            'option_type_device'         => Yii::t('app', 'Тип устройства'),
            'link_url_1'                 => Yii::t('app', 'Ссылка-1'),
            'link_url_2'                 => Yii::t('app', 'Ссылка-2'),
            'link_url_3'                 => Yii::t('app', 'Ссылка-3'),
            'link_url_4'                 => Yii::t('app', 'Ссылка-4'),
            'link_url_5'                 => Yii::t('app', 'Ссылка-5'),
            'link_url_6'                 => Yii::t('app', 'Ссылка-6'),
            'link_name_1'                => Yii::t('app', 'Название ссылки'),
            'link_name_2'                => Yii::t('app', 'Название ссылки'),
            'link_name_3'                => Yii::t('app', 'Название ссылки'),
            'link_name_4'                => Yii::t('app', 'Название ссылки'),
            'link_name_5'                => Yii::t('app', 'Название ссылки'),
            'link_name_6'                => Yii::t('app', 'Название ссылки'),
            'link_logo_1'                => Yii::t('app', 'Логотип'),
            'link_logo_2'                => Yii::t('app', 'Логотип'),
            'link_logo_3'                => Yii::t('app', 'Логотип'),
            'link_logo_4'                => Yii::t('app', 'Логотип'),
            'link_logo_5'                => Yii::t('app', 'Логотип'),
            'link_logo_6'                => Yii::t('app', 'Логотип'),
            'text_for_negative'          => Yii::t('app', 'Текст для тех, кто оставил негативную оценку'),
            'text_for_positive'          => Yii::t('app', 'Текст для тех, кто оставил позитивную оценку'),
            'option_is_auto_open_window' => Yii::t('app', 'Включить автоматическое открытие окошка виджета при загрузке страницы'),
            'option_is_close_button'     => Yii::t('app', 'Отключить вывод кнопки на сайте'),
            'hash'                       => Yii::t('app', 'Hash для генерации отдельной страницы'),
            'is_generate_target_link'    => Yii::t('app', 'Сгенерировать прямую ссылку на страницу с виджетом'),
            'bg_color_target_page'       => Yii::t('app', 'Цвет фона отдельной страницы'),
            'created_at'                 => Yii::t('app', 'Created At'),
            'updated_at'                 => Yii::t('app', 'Updated At'),
        ];
    }


    /**
     * Gets query for [[FeedbackLinks]].
     *
     * @return \yii\db\ActiveQuery|FeedbackLinkQuery
     */
    public function getFeedbackLinks()
    {
        return $this->hasMany(FeedbackLink::className(), ['feedback_widget_id' => 'id']);
    }


    /**
     * Gets query for [[FeedbackResults]].
     *
     * @return \yii\db\ActiveQuery|FeedbackResultQuery
     */
    public function getFeedbackResults()
    {
        return $this->hasMany(FeedbackResult::className(), ['feedback_widget_id' => 'id']);
    }


    /**
     * Gets query for [[FeedbackProject]].
     *
     * @return \yii\db\ActiveQuery|FeedbackProjectQuery
     */
    public function getFeedbackProject()
    {
        return $this->hasOne(FeedbackProject::className(), ['id' => 'feedback_project_id']);
    }


    /**
     * {@inheritdoc}
     * @return FeedbackWidgetQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FeedbackWidgetQuery(get_called_class());
    }


    /**
     * Default
     *
     * @param      $type
     * @param null $code
     *
     * @return bool
     */
    public static function itemAlias($type, $code = NULL)
    {
        $_items = array(
            'Status' => array(
                self::STATUS_ACTIVED => [\Yii::t('web', 'Активные'), 'index'],
                self::STATUS_STOPED  => [\Yii::t('web', 'Приостановленые'), 'stoped'],
            ),
        );
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }


    /**
     * @return mixed
     */
    public static function collectionActived()
    {
        // фильтр
        $feedbackProjectId = isset(Yii::$app->request->get('FeedbackWidgetSearch')['feedback_project_id']) ? Yii::$app->request->get('FeedbackWidgetSearch')['feedback_project_id'] : null;

        $query = self::find()
            ->alias('t')
            ->joinWith(['feedbackProject as fp'])
            ->andWhere(['fp.user_id' => \Yii::$app->user->getId()])
            ->andWhere(['t.status' => self::STATUS_ACTIVED]);

        // фильтр
        if ($feedbackProjectId) {
            $query->andWhere(['t.feedback_project_id' => $feedbackProjectId]);
        }

        $models = $query->asArray()->all();

        return ArrayHelper::map($models, 'id', 'name');
    }


    public static function getModel($id)
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
    }


    /**
     * Берем первый активный виджет по токену проекта
     *
     * @param $token  - токен проекта
     * @param $domain - домен виджета где будет установлен
     */
    public static function getActiveByTokenProject($token, $domain, $slug = null)
    {
        $query = self::find()
            ->alias('t')
            ->joinWith(['feedbackProject as fp'])
            ->andWhere(['fp.domain' => $domain])
            ->andWhere(['fp.token' => $token])
            ->andWhere(['t.type' => self::TYPE_REVIEW])
            ->andWhere(['t.status' => self::STATUS_ACTIVED]);

        $model = $query->one();

        return $model;
    }
}