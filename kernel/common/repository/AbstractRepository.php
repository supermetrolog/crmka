<?php

declare(strict_types=1);

namespace app\kernel\common\repository;

use app\helpers\ArrayHelper;

abstract class AbstractRepository implements RepositoryInterface
{
	protected array $with;

	public function with(array $relations): self
	{
		$cloned = clone $this;

		$cloned->with = ArrayHelper::merge($cloned->with, $relations);

		return $cloned;
	}
}