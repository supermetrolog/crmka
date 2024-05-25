<?php

declare(strict_types=1);

namespace app\usecases\Reminder;

use app\dto\Reminder\CreateReminderDto;
use app\dto\Reminder\CreateReminderForUsersDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Reminder;
use Throwable;

class CreateReminderService
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
	public function create(CreateReminderDto $dto): Reminder
	{
		$reminder = new Reminder([
			'user_id'         => $dto->user->id,
			'message'         => $dto->message,
			'status'          => $dto->status,
			'created_by_type' => $dto->created_by_type,
			'created_by_id'   => $dto->created_by_id,
			'notify_at'       => $dto->notify_at ? $dto->notify_at->format('Y-m-d H:i:s') : null,
		]);

		$reminder->saveOrThrow();

		return $reminder;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function createForUsers(CreateReminderForUsersDto $dto): array
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$reminders = [];

			foreach ($dto->users as $user) {
				$reminders[] = $this->create(new CreateReminderDto([
					'user'            => $user,
					'message'         => $dto->message,
					'status'          => $dto->status,
					'created_by_type' => $dto->created_by_type,
					'created_by_id'   => $dto->created_by_id,
					'notify_at'       => $dto->notify_at,
				]));
			}

			$tx->commit();

			return $reminders;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}