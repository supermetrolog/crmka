<?php

namespace app\components\EffectStrategy\Factory;


use app\components\EffectStrategy\EffectStrategyInterface;
use app\components\EffectStrategy\Strategies\CompaniesOnObjectIdentifiedEffectStrategy;
use app\components\EffectStrategy\Strategies\CompanyDoesNotWantToSellEffectStrategy;
use app\components\EffectStrategy\Strategies\CompanyRequestsChangesEffectStrategy;
use app\components\EffectStrategy\Strategies\CompanyRequestsCreatedEffectStrategy;
use app\components\EffectStrategy\Strategies\CompanyWantsToSellMustBeEditedEffectStrategy;
use app\components\EffectStrategy\Strategies\HasActualRequestsEffectStrategy;
use app\components\EffectStrategy\Strategies\HasEquipmentsOffersEffectStrategy;
use app\components\EffectStrategy\Strategies\HasEquipmentsRequestsEffectStrategy;
use app\components\EffectStrategy\Strategies\HasNewOffersEffectStrategy;
use app\components\EffectStrategy\Strategies\HasNewRequestsEffectStrategy;
use app\components\EffectStrategy\Strategies\ObjectFreeAreaMustBeEditedEffectStrategy;
use app\components\EffectStrategy\Strategies\ObjectHasFreeAreaEffectStrategy;
use app\enum\EffectKind;
use app\helpers\ArrayHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class EffectStrategyFactory implements EffectStrategyFactoryInterface
{
	private array $strategies = [
		EffectKind::COMPANIES_ON_OBJECT_IDENTIFIED       => CompaniesOnObjectIdentifiedEffectStrategy::class,
		EffectKind::OBJECT_HAS_FREE_AREA                 => ObjectHasFreeAreaEffectStrategy::class,
		EffectKind::OBJECT_FREE_AREA_MUST_BE_EDITED      => ObjectFreeAreaMustBeEditedEffectStrategy::class,
		EffectKind::COMPANY_DOES_NOT_WANT_TO_SELL        => CompanyDoesNotWantToSellEffectStrategy::class,
		EffectKind::COMPANY_WANTS_TO_SELL_MUST_BE_EDITED => CompanyWantsToSellMustBeEditedEffectStrategy::class,
		EffectKind::HAS_ACTUAL_REQUESTS                  => HasActualRequestsEffectStrategy::class,
		EffectKind::HAS_EQUIPMENTS_OFFERS                => HasEquipmentsOffersEffectStrategy::class,
		EffectKind::HAS_EQUIPMENTS_REQUESTS              => HasEquipmentsRequestsEffectStrategy::class,
		EffectKind::HAS_NEW_REQUESTS                     => HasNewRequestsEffectStrategy::class,
		EffectKind::HAS_NEW_OFFERS                       => HasNewOffersEffectStrategy::class,
		EffectKind::CURRENT_REQUESTS_STEP                => CompanyRequestsChangesEffectStrategy::class,
		EffectKind::CREATED_REQUESTS_STEP                => CompanyRequestsCreatedEffectStrategy::class,

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
