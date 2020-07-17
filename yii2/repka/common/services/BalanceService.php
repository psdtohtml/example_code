<?php

namespace common\services;

use common\models\SystemInfo;
use DateTime;
use Exception;
use RuntimeException;
use Yii;

/**
 * Сервис баланса СЕО-сервиса.
 */
class BalanceService
{
    /**
     * @var \common\services\ISeoService
     */
    private $seoService;

    public function __construct(ISeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    /**
     * Актуализация баланса
     *
     * @throws \Exception
     */
    public function actualizeBalance(): void
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $balance = $this->seoService->getAccountBalance();
            $date = new DateTime();

            $balanceInfo = SystemInfo::findOne(['key' => SystemInfo::KEY_SEO_BALANCE]);
            $actualizedAtInfo = SystemInfo::findOne(['key' => SystemInfo::KEY_SEO_BALANCE_ACTUALIZED_AT]);

            if (!$balanceInfo || !$actualizedAtInfo) {
                throw new RuntimeException('Не задан набор сведений о системе.');
            }

            $balanceInfo->value = (string)$balance;
            $balanceInfo->save();

            $actualizedAtInfo->value = $date->format('Y-m-d H:i:s');
            $actualizedAtInfo->save();

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::error(['Ошибка при актуализации баланса.', $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Возвращает сведения о балансе.
     *
     * @return array ['balance' => null|float, 'actualizedAt' => null|\DateTime]
     * @throws \Exception
     */
    public function getBalance(): array
    {
        return [
            'balance' => SystemInfo::getValue(SystemInfo::KEY_SEO_BALANCE),
            'actualizedAt' => SystemInfo::getValue(SystemInfo::KEY_SEO_BALANCE_ACTUALIZED_AT),
        ];
    }
}
