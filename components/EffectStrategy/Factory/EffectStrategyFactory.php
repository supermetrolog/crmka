<?php

namespace app\components\EffectStrategy\Factory;


use app\components\EffectStrategy\CompaniesOnObjectIdentifiedEffectStrategy;
use app\components\EffectStrategy\CompanyPlannedDevelopEffectStrategy;
use app\components\EffectStrategy\EffectStrategyInterface;
use app\components\EffectStrategy\RequestsNoLongerRelevantEffectStrategy;
use app\enum\EffectKind;
use app\helpers\ArrayHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class EffectStrategyFactory implements EffectStrategyFactoryInterface
{
	private array $strategies = [
		EffectKind::REQUESTS_NO_LONGER_RELEVANT    => RequestsNoLongerRelevantEffectStrategy::class,
		EffectKind::COMPANY_PLANNED_DEVELOP        => CompanyPlannedDevelopEffectStrategy::class,
		EffectKind::COMPANIES_ON_OBJECT_IDENTIFIED => CompaniesOnObjectIdentifiedEffectStrategy::class
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
