<?php

namespace app\kernel\common\repository;

use app\kernel\common\models\AR\AR;

interface RepositoryInterface
{
	public function findOne(int $id): ?AR;

	public function findOneOrThrow(int $id);

	/**
	 * @return AR[]
	 */
	public function findAll(): array;

	public function with(array $relations): self;
}