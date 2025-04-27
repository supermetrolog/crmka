<?php

declare(strict_types=1);

namespace app\usecases\Folder;

use app\dto\Folder\CreateFolderDto;
use app\dto\Folder\EntityInFolderDto;
use app\dto\Folder\FolderEntityDto;
use app\dto\Folder\ReorderFolderDto;
use app\dto\Folder\UpdateFolderDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Folder;
use ErrorException;
use Throwable;
use yii\base\InvalidArgumentException;
use yii\db\StaleObjectException;

class FolderService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private FolderEntityService          $entityService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		FolderEntityService $entityService
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->entityService       = $entityService;
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateFolderDto $dto): Folder
	{
		$folder = new Folder([
			'user_id'    => $dto->user->id,
			'name'       => $dto->name,
			'color'      => $dto->color,
			'icon'       => $dto->icon,
			'sort_order' => Folder::DEFAULT_SORT_ORDER,
			'category'   => $dto->category
		]);

		$folder->saveOrThrow();

		return $folder;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(Folder $folder, UpdateFolderDto $dto): Folder
	{
		$folder->load([
			'name'  => $dto->name,
			'color' => $dto->color,
			'icon'  => $dto->icon
		]);

		$folder->saveOrThrow();

		return $folder;
	}

	/**
	 * @throws SaveModelException
	 * @throws ErrorException
	 */
	public function addEntityToFolder(Folder $folder, EntityInFolderDto $dto): void
	{
		if ($folder->hasEntityByTypeAndEntityId($dto->entity_type, $dto->entity_id)) {
			return;
		}

		$this->entityService->create(
			new FolderEntityDto([
				'folder'      => $folder,
				'entity_id'   => $dto->entity_id,
				'entity_type' => $dto->entity_type
			])
		);
	}

	/**
	 * @param EntityInFolderDto[] $dtos
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function addEntitiesToFolder(Folder $folder, array $dtos): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($dtos as $dto) {
				$this->addEntityToFolder($folder, $dto);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 * @throws ErrorException
	 */
	public function removeEntityFromFolder(Folder $folder, EntityInFolderDto $dto): void
	{
		$entity = $folder->getEntities()->byEntityId($dto->entity_id)->byType($dto->entity_type)->one();

		if ($entity) {
			$this->entityService->delete($entity);
		}
	}

	/**
	 * @param EntityInFolderDto[] $dtos
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function removeEntitiesFromFolder(Folder $folder, array $dtos): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($dtos as $dto) {
				$this->removeEntityFromFolder($folder, $dto);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @param ReorderFolderDto[] $dtos
	 *
	 * @throws Throwable
	 */
	public function reorderFolders(array $dtos): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($dtos as $dto) {
				$this->updateSortOrder($dto->folder, $dto->sortOrder);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	public function updateSortOrder(Folder $folder, int $position): void
	{
		if ($position < 0) {
			throw new InvalidArgumentException('Invalid folder position');
		}

		$folder->updateAttributes(['sort_order' => $position]);
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Folder $folder): void
	{
		$folder->delete();
	}
}