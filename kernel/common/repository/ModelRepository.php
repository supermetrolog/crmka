<?php

namespace app\kernel\common\repository;

use app\helpers\ArrayHelper;
use app\kernel\common\models\AQ\ModelAQ;
use app\kernel\common\models\AR\AR;
use app\kernel\common\models\exceptions\ModelNotFoundException;

/**
 * @template-covariant Model of AR
 */
abstract class ModelRepository implements RepositoryInterface
{
	/** @var class-string<Model> */
	protected string $className;

	protected array $with;

	private function find(): ModelAQ
	{
		return $this->className::find();
	}

	/**
	 * @return Model|null
	 */
	public function findOne(int $id, bool $notDeleted = true): ?AR
	{
		$query = $this->find()->byId($id);

		if ($notDeleted) {
			$query->notDeleted();
		}

		return $query->one();
	}

	/**
	 * @return Model
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id, bool $notDeleted = true): AR
	{
		$query = $this->find()->byId($id);

		if ($notDeleted) {
			$query->notDeleted();
		}

		return $query->oneOrThrow();
	}

	/**
	 * @return Model[]
	 */
	public function findAll(): array
	{
		return $this->className::find()->all();
	}

	public function with(array $relations): self
	{
		$cloned = clone $this;

		$cloned->with = ArrayHelper::merge($cloned->with, $relations);

		return $cloned;
	}
}