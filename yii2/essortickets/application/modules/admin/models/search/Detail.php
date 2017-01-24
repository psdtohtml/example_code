<?php
namespace app\modules\admin\models\search;

use app\enums\Days;
use Yii;
use yii\data\Sort;
use yii\data\Pagination;
use yii\db\Query;

use app\base\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use app\models\Detail as DetailModel;

/**
 * Search for Detail model.
 * @inheritdoc
 * @package app\modules\admin\models\search
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Detail extends DetailModel
{
    use \app\models\traits\ColumnFilter {
        getSuggestions as traitGetSuggestions;
    }

    /** @var integer $monday Day. */
    public $monday;
    /** @var integer tuesday Day. */
    public $tuesday;
    /** @var integer $wednesday Day. */
    public $wednesday;
    /** @var integer $thursday Day. */
    public $thursday;
    /** @var integer $friday Day. */
    public $friday;
    /** @var integer $saturday Day. */
    public $saturday;
    /** @var integer $sunday Day. */
    public $sunday;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'availability_id', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday','saturday', 'sunday'], 'integer'],
            [['time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'monday' => Yii::t('app', 'Monday'),
            'tuesday' => Yii::t('app', 'Tuesday'),
            'wednesday' => Yii::t('app', 'Wednesday'),
            'thursday' => Yii::t('app', 'Thursday'),
            'friday' => Yii::t('app', 'Friday'),
            'saturday' => Yii::t('app', 'Saturday'),
            'sunday' => Yii::t('app', 'Sunday'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
    }

    /**
     * @param array $params for search.
     * @return ArrayDataProvider
     */
    public function search(array $params)
    {
        $this->setQuerySeason($params['id']);

        $this->query->each();

        $queryResult = $this->query->createCommand()->queryAll();

        $result=[];
        foreach($queryResult as $item) {
            if(!isset($result[$item['time']]['time'])){

                $result[$item['time']]['time'] = $item['time'];
            }
            $result[$item['time']]['name'] = $item['name'];
            $result[$item['time']]['capacity'] = $item['capacity'];
            $result[$item['time']][Days::getDayById($item['day'])]=$item['capacity'];
        }

        return new ArrayDataProvider([
            'allModels'  => $result,
            'key'        => 'time',
            'pagination' => false,
        ]);
    }

    /**
     * Set Query.
     * @internal param $fileInRotation
     */
    protected function setQuerySeason($id)
    {
        $this->query = new Query();
        $this->query->select([
            'id'              => 'd.id',
            'name'            => 'd.name',
            'availability_id' => 'd.availability_id',
            'time'            => 'd.time',
            'day'             => 'd.day',
            'capacity'        => 'd.capacity'
        ])
            ->from(['d' => static::tableName()])
            ->where(['d.availability_id'=>$id]);
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
    }
}
