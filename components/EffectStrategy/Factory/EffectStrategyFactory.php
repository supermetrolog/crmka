<?php

namespace app\components\EffectStrategy\Factory;


use app\components\EffectStrategy\EffectStrategyInterface;
use app\components\EffectStrategy\Strategies\CompaniesOnObjectIdentifiedEffectStrategy;
use app\components\EffectStrategy\Strategies\CompanyDoesNotWantToSellEffectStrategy;
use app\components\EffectStrategy\Strategies\CompanyHasNewRequestEffectStrategy;
use app\components\EffectStrategy\Strategies\CompanyHasSubleaseOrStorageEffectStrategy;
use app\components\EffectStrategy\Strategies\CompanyPlannedDevelopEffectStrategy;
use app\components\EffectStrategy\Strategies\CompanyWantsToBuyOrBuildEffectStrategy;
use app\components\EffectStrategy\Strategies\CompanyWantsToBuyOrSellEquipmentEffectStrategy;
use app\components\EffectStrategy\Strategies\CompanyWantsToSellMustBeEditedEffectStrategy;
use app\components\EffectStrategy\Strategies\HasActualRequestsEffectStrategy;
use app\components\EffectStrategy\Strategies\ObjectFreeAreaMustBeDeletedEffectStrategy;
use app\components\EffectStrategy\Strategies\ObjectFreeAreaMustBeEditedEffectStrategy;
use app\components\EffectStrategy\Strategies\ObjectHasEquipmentForSaleEffectStrategy;
use app\components\EffectStrategy\Strategies\RequestsNoLongerRelevantEffectStrategy;
use app\enum\EffectKind;
use app\helpers\ArrayHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class EffectStrategyFactory implements EffectStrategyFactoryInterface
{
	private array $strategies = [
		EffectKind::REQUESTS_NO_LONGER_RELEVANT            => RequestsNoLongerRelevantEffectStrategy::class,
		EffectKind::COMPANY_PLANNED_DEVELOP                => CompanyPlannedDevelopEffectStrategy::class,
		EffectKind::COMPANIES_ON_OBJECT_IDENTIFIED         => CompaniesOnObjectIdentifiedEffectStrategy::class,
		EffectKind::COMPANY_WANTS_TO_SELL_MUST_BE_EDITED   => CompanyWantsToSellMustBeEditedEffectStrategy::class,
		EffectKind::COMPANY_WANTS_TO_BUY_OR_BUILD          => CompanyWantsToBuyOrBuildEffectStrategy::class,
		EffectKind::COMPANY_HAS_NEW_REQUEST                => CompanyHasNewRequestEffectStrategy::class,
		EffectKind::COMPANY_WANTS_TO_BUY_OR_SELL_EQUIPMENT => CompanyWantsToBuyOrSellEquipmentEffectStrategy::class,
		EffectKind::OBJECT_HAS_EQUIPMENT_FOR_SALE          => ObjectHasEquipmentForSaleEffectStrategy::class,
		EffectKind::COMPANY_HAS_SUBLEASE_OR_STORAGE        => CompanyHasSubleaseOrStorageEffectStrategy::class,
		EffectKind::OBJECT_FREE_AREA_MUST_BE_EDITED        => ObjectFreeAreaMustBeEditedEffectStrategy::class,
		EffectKind::OBJECT_FREE_AREA_MUST_BE_DELETED       => ObjectFreeAreaMustBeDeletedEffectStrategy::class,
		EffectKind::COMPANY_DOES_NOT_WANT_TO_SELL          => CompanyDoesNotWantToSellEffectStrategy::class,

		EffectKind::HAS_ACTUAL_REQUESTS => HasActualRequestsEffectStrategy::class
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
