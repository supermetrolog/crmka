<?php

declare(strict_types=1);

namespace app\usecases\Request\Relations;

use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Request;
use Throwable;

abstract class AbstractRequestRelationService
{
	protected TransactionBeginnerInterface $transactionBeginner;

	/** @var class-string $relationClass */
	protected string $relationClass;
	protected string $relationAttribute;
	protected string $relationGetter;

	public function __construct(TransactionBeginnerInterface $transactionBeginner)
	{
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @param array<string|int> $relationIds
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function createRelations(Request $request, array $relationIds): array
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$models = [];

			foreach ($relationIds as $relationId) {
				$models[] = $this->createRelation($request, $relationId);
			}

			$tx->commit();

			return $models;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @param int|string $relationId
	 *
	 * @throws SaveModelException
	 */
	public function createRelation(Request $request, $relationId)
	{
		$model = new $this->relationClass([
			'request_id'             => $request->id,
			$this->relationAttribute => $relationId
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @param int[] $newIds
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function updateRelations(Request $request, array $newIds = []): void
	{
		if (ArrayHelper::empty($newIds)) {
			$this->deleteAllRelations($request);

			return;
		}

		$oldIds = $this->mapRelationModelsToRelationAttributes($request->{$this->relationGetter});

		$deletedIds = ArrayHelper::diff($oldIds, $newIds);
		$addedIds   = ArrayHelper::diff($newIds, $oldIds);

		$tx = $this->transactionBeginner->begin();

		try {
			$this->deleteRelations($request, $deletedIds);
			$this->createRelations($request, $addedIds);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/** @param int[] $ids */
	public function deleteRelations(Request $request, array $ids): void
	{
		($this->relationClass)::deleteAll([
			'request_id'             => $request->id,
			$this->relationAttribute => $ids
		]);
	}

	private function deleteAllRelations(Request $request): void
	{
		($this->relationClass)::deleteAll([
			'request_id' => $request->id
		]);
	}

	/** @return int[] */
	private function mapRelationModelsToRelationAttributes(array $relations): array
	{
		return ArrayHelper::map($relations, fn($relation) => ArrayHelper::getValue($relation, $this->relationAttribute));
	}
}
