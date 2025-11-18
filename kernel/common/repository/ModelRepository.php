<?php

namespace app\kernel\common\repository;

use app\helpers\ArrayHelper;
use app\kernel\common\models\AR\AR;
use app\kernel\common\models\exceptions\ModelNotFoundException;

/**
 * @template-covariant Model of AR
 */
abstract class ModelRepository
{
	/** @var class-string<Model> */
	protected $className;

	protected array $with;

	/**
	 * @param class-string<Model> $className
	 */
	public function __construct(string $className)
	{
		$this->className = $className;
	}

	// Repository methods

	/**
	 * @return Model|null
	 */
	public function findOne(int $id, $notDeleted = true)
	{
		if ($notDeleted) {
			return $this->className::find()->notDeleted()->byId($id)->one();
		} else {
			return $this->className::find()->byId($id)->one();
		}
	}

	/**
	 * @return Model
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id, $notDeleted = true)
	{
		if ($notDeleted) {
			return $this->className::find()->notDeleted()->byId($id)->oneOrThrow();
		} else {
			return $this->className::find()->byId($id)->oneOrThrow();
		}
	}

	/**
	 * @return Model[]
	 */
	public function findAll()
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