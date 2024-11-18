<?php

namespace app\components\EffectStrategy\Factory;

use app\components\EffectStrategy\EffectStrategyInterface;

interface EffectStrategyFactoryInterface
{
	public function createStrategy(string $effectKind): EffectStrategyInterface;
}