<?php

namespace app\mappers;

use app\kernel\common\models\AR\AR;
use app\kernel\common\repository\RepositoryInterface;

abstract class AbstractDtoMapper
{
	/**
	 * @param ?string|int $id
	 */
	protected function findOrNull(RepositoryInterface $repository, $id): ?AR
	{
		return $id ? $repository->findOne((int)$id) : null;
	}
}