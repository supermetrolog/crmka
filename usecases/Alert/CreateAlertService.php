<?php

declare(strict_types=1);

namespace app\usecases\Alert;

use app\dto\Alert\CreateAlertDto;
use app\dto\Alert\CreateAlertForUsersDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Alert;
use Throwable;

class CreateAlertService
{
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateAlertDto $dto): Alert
	{
		$task = new Alert([
			'user_id'         => $dto->user->id,
			'message'         => $dto->message,
			'created_by_type' => $dto->created_by_type,
			'created_by_id'   => $dto->created_by_id,
		]);

		$task->saveOrThrow();

		return $task;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function createForUsers(CreateAlertForUsersDto $dto): array
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$tasks = [];

			foreach ($dto->users as $user) {
				$tasks[] = $this->create(new CreateAlertDto([
					'user'            => $user,
					'message'         => $dto->message,
					'created_by_type' => $dto->created_by_type,
					'created_by_id'   => $dto->created_by_id,
				]));
			}

			$tx->commit();

			return $tasks;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}