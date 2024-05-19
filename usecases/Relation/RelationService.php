<?php

declare(strict_types=1);

namespace app\usecases\Relation;

use app\dto\Relation\CreateRelationDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ActiveQuery\RelationQuery;
use app\models\Relation;
use Throwable;
use yii\db\StaleObjectException;

class RelationService
{
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(TransactionBeginnerInterface $transactionBeginner)
	{
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateRelationDto $dto): Relation
	{
		$model = new Relation();

		$model->first_type  = $dto->first_type;
		$model->first_id    = $dto->first_id;
		$model->second_type = $dto->second_type;
		$model->second_id   = $dto->second_id;

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function createIfNotExists(CreateRelationDto $dto): Relation
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$query = Relation::find()
			                 ->byFirst($dto->first_id, $dto->first_type)
			                 ->bySecond($dto->second_id, $dto->second_type);

			$relation = $query->one();

			if (!$relation) {
				$relation = $this->create($dto);
			}

			$tx->commit();

			return $relation;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function deleteByOwner(int $first_id, string $first_type, string $second_type): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$query = Relation::find()
			                 ->byFirst($first_id, $first_type)
			                 ->bySecondType($second_type);

			/** @var Relation $rel */
			foreach ($query->each() as $rel) {
				$rel->delete();
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	public function deleteByQuery(RelationQuery $query): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			/** @var Relation $rel */
			foreach ($query->each() as $rel) {
				$rel->delete();
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}