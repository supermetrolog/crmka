<?php

namespace app\components\EffectStrategy\Factory;


use app\components\EffectStrategy\EffectStrategyInterface;
use app\components\EffectStrategy\Strategies\CompanyOffersChangesEffectStrategy;
use app\components\EffectStrategy\Strategies\CompanyOffersCreatedEffectStrategy;
use app\components\EffectStrategy\Strategies\CompanyRequestsChangesEffectStrategy;
use app\components\EffectStrategy\Strategies\CompanyRequestsCreatedEffectStrategy;
use app\components\EffectStrategy\Strategies\HasEquipmentsOffersEffectStrategy;
use app\components\EffectStrategy\Strategies\HasEquipmentsRequestsEffectStrategy;
use app\enum\EffectKind;
use app\helpers\ArrayHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class EffectStrategyFactory implements EffectStrategyFactoryInterface
{
	private array $strategies = [
		EffectKind::HAS_EQUIPMENTS_OFFERS   => HasEquipmentsOffersEffectStrategy::class,
		EffectKind::HAS_EQUIPMENTS_REQUESTS => HasEquipmentsRequestsEffectStrategy::class,
		EffectKind::CURRENT_REQUESTS_STEP   => CompanyRequestsChangesEffectStrategy::class,
		EffectKind::CREATED_REQUESTS_STEP   => CompanyRequestsCreatedEffectStrategy::class,
		EffectKind::CREATED_OFFERS_STEP     => CompanyOffersCreatedEffectStrategy::class,
		EffectKind::CURRENT_OFFERS_STEP     => CompanyOffersChangesEffectStrategy::class,

	];

	public function hasStrategy(string $effectKind): bool
	{
		return ArrayHelper::keyExists($this->strategies, $effectKind);
	}

	/**
	 * @throws NotInstantiableException
	 * @throws InvalidConfigException
	 */
	public function createStrategy(string $effectKind): EffectStrategyInterface
	{
		$strategyClass = $this->strategies[$effectKind];

		return Yii::$container->get($strategyClass);
	}
}
