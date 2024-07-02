<?php

declare(strict_types=1);

namespace app\usecases\Equipment;

use app\dto\Equipment\CreateEquipmentDto;
use app\dto\Equipment\UpdateEquipmentDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Equipment;
use Throwable;
use yii\db\StaleObjectException;

class EquipmentService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateEquipmentDto $dto): Equipment
	{
		$model = new Equipment([
			'name'            => $dto->name,
			'address'         => $dto->address,
			'description'     => $dto->description,
			'company_id'      => $dto->company_id,
			'contact_id'      => $dto->contact_id,
			'consultant_id'   => $dto->consultant_id,
			'preview_id'      => $dto->preview_id,
			'category'        => $dto->category,
			'availability'    => $dto->availability,
			'delivery'        => $dto->delivery,
			'deliveryPrice'   => $dto->deliveryPrice,
			'price'           => $dto->price,
			'benefit'         => $dto->benefit,
			'tax'             => $dto->tax,
			'count'           => $dto->count,
			'state'           => $dto->state,
			'status'          => $dto->status,
			'passive_type'    => $dto->passive_type,
			'passive_comment' => $dto->passive_comment,
			'created_by_type' => $dto->created_by_type,
			'created_by_id'   => $dto->created_by_id,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(Equipment $model, UpdateEquipmentDto $dto): Equipment
	{
		$model->load([
			'name'            => $dto->name,
			'address'         => $dto->address,
			'description'     => $dto->description,
			'company_id'      => $dto->company_id,
			'contact_id'      => $dto->contact_id,
			'consultant_id'   => $dto->consultant_id,
			'preview_id'      => $dto->preview_id,
			'category'        => $dto->category,
			'availability'    => $dto->availability,
			'delivery'        => $dto->delivery,
			'deliveryPrice'   => $dto->deliveryPrice,
			'price'           => $dto->price,
			'benefit'         => $dto->benefit,
			'tax'             => $dto->tax,
			'count'           => $dto->count,
			'state'           => $dto->state,
			'status'          => $dto->status,
			'passive_type'    => $dto->passive_type,
			'passive_comment' => $dto->passive_comment,
			'created_by_type' => $dto->created_by_type,
			'created_by_id'   => $dto->created_by_id,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Equipment $model): void
	{
		$model->delete();
	}
}