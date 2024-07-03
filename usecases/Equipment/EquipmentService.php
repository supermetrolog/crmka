<?php

declare(strict_types=1);

namespace app\usecases\Equipment;

use app\dto\Call\CreateCallDto;
use app\dto\Equipment\CreateEquipmentDto;
use app\dto\Equipment\UpdateEquipmentDto;
use app\dto\Media\CreateMediaDto;
use app\dto\Relation\CreateRelationDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Call;
use app\models\ChatMember;
use app\models\Equipment;
use app\usecases\Call\CreateCallService;
use app\usecases\Media\CreateMediaService;
use app\usecases\Relation\RelationService;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

class EquipmentService
{
	private TransactionBeginnerInterface $transactionBeginner;
	protected CreateMediaService         $createMediaService;
	protected RelationService            $relationService;
	protected CreateCallService          $createCallService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		RelationService $relationService,
		CreateMediaService $createMediaService,
		CreateCallService $createCallService
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->relationService     = $relationService;
		$this->createMediaService  = $createMediaService;
		$this->createCallService   = $createCallService;
	}

	/**
	 * @param CreateMediaDto[] $previewDtos
	 * @param CreateMediaDto[] $filesDtos
	 * @param CreateMediaDto[] $photosDtos
	 *
	 * @throws SaveModelException
	 */
	public function create(CreateEquipmentDto $dto, array $previewDtos, array $filesDtos, array $photosDtos): Equipment
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$preview = $this->createMediaService->create($previewDtos[0]);

			$model = new Equipment([
				'name'            => $dto->name,
				'address'         => $dto->address,
				'description'     => $dto->description,
				'company_id'      => $dto->company_id,
				'contact_id'      => $dto->contact_id,
				'consultant_id'   => $dto->consultant_id,
				'preview_id'      => $preview->id,
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

			$this->relationService->create(new CreateRelationDto([
				'first_type'  => $model::getMorphClass(),
				'first_id'    => $model->id,
				'second_type' => $preview::getMorphClass(),
				'second_id'   => $preview->id,
			]));

			foreach ($filesDtos as $mediaDto) {
				$media = $this->createMediaService->create($mediaDto);

				$this->relationService->create(new CreateRelationDto([
					'first_type'  => $model::getMorphClass(),
					'first_id'    => $model->id,
					'second_type' => $media::getMorphClass(),
					'second_id'   => $media->id,
				]));
			}

			foreach ($photosDtos as $mediaDto) {
				$media = $this->createMediaService->create($mediaDto);

				$this->relationService->create(new CreateRelationDto([
					'first_type'  => $model::getMorphClass(),
					'first_id'    => $model->id,
					'second_type' => $media::getMorphClass(),
					'second_id'   => $media->id,
				]));
			}

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
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

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function createCall(Equipment $equipment, CreateCallDto $dto): Call
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model = $this->createCallService->create($dto);

			$this->relationService->create(new CreateRelationDto([
				'first_type'  => $equipment::getMorphClass(),
				'first_id'    => $equipment->id,
				'second_type' => $model::getMorphClass(),
				'second_id'   => $model->id,
			]));

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}
}