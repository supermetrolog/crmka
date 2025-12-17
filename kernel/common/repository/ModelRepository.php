<?php

namespace app\kernel\common\repository;

use app\kernel\common\models\AR\AR;
use app\kernel\common\models\exceptions\ModelNotFoundException;

/**
 * @template-covariant Model of AR
 */
abstract class ModelRepository implements RepositoryInterface
{
	/** @var class-string<Model> */
	protected string $className;

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

	private function find()
	{
		return $this->className::find();
	}
}