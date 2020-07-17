<?php


namespace common\services;


use common\models\SystemSettings;
use yii\caching\Cache;
use yii\di\Instance;
use yii\web\NotFoundHttpException;

/**
 * Сервис системных настроек.
 */
class SystemSettingsService
{
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var array данные настроек
     */
    private $data;

    /**
     * @var string ключ кэша
     */
    private $cacheKey = 'system-settings';

    /**
     * Конструктор.
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct()
    {
        $this->cache = Instance::ensure('cache', Cache::class);
        $this->loadData();
    }

    /**
     * Сохраняет значение запрошенной настройки.
     * При успешном сохранении производится сброс кэша настроек.
     *
     * @param string $key ключ настройки (константы \common\models\SystemSettings::KEY_*)
     * @param string $value значение
     * @return bool
     * @throws \yii\web\NotFoundHttpException
     */
    public function save(string $key, string $value): bool
    {
        $item = $this->findByKey($key);

        $item->value = trim($value);
        $result = $item->save(false);

        if ($result) {
            $this->dropCache();
        }

        return $result;
    }

    /**
     * Возвращает набор моделей системных настроек.
     *
     * @return array|\common\models\SystemSettings[]
     */
    public function getSettings(): array
    {
        return $this->data;
    }

    /**
     * Возвращает значение настройки по запрошенному ключу.
     *
     * @param string $key ключ настройки (константы \common\models\SystemSettings::KEY_*)
     * @return string
     */
    public function getValue(string $key): string
    {
        return isset($this->data[$key]) ? $this->data[$key]->value : null;
    }

    /**
     * Инвалидация кэша настроек.
     */
    public function dropCache(): void
    {
        $this->cache->delete($this->cacheKey);
    }

    /**
     * Загрузка данных настроек.
     */
    private function loadData()
    {
        $this->data = $this->cache->getOrSet(
            $this->cacheKey,
            function () {
                $list = [];
                foreach (SystemSettings::find()->all() as $item) {
                    $list[$item->key] = $item;
                }
                return $list;
            }
        );
    }

    /**
     * Возвращает объект настроки запрошенный по ключу.
     *
     * @param string $key ключ настройки (константы \common\models\SystemSettings::KEY_*)
     * @return \common\models\SystemSettings
     * @throws \yii\web\NotFoundHttpException
     */
    private function findByKey(string $key): SystemSettings
    {
        $item = SystemSettings::find()->withKey($key)->one();

        if (null === $item) {
            throw new NotFoundHttpException('Запрошенная настройка не найдена');
        }

        return $item;
    }
}
