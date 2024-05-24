<?php

declare(strict_types=1);

namespace app\usecases\Reminder;

use app\dto\Reminder\UpdateReminderDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Reminder;
use Throwable;
use UnexpectedValueException;
use yii\db\StaleObjectException;

class ReminderService
{

	/**
	 * @throws SaveModelException
	 */
	public function update(Reminder $Reminder, UpdateReminderDto $dto): Reminder
	{
		$Reminder->load([
			'user_id'   => $dto->user->id,
			'message'   => $dto->message,
			'status'    => $dto->status,
			'notify_at' => $dto->notify_at ? $dto->notify_at->format('Y-m-d H:i:s') : null,
		]);

		$Reminder->saveOrThrow();

		return $Reminder;
	}

	/**
	 * @throws SaveModelException
	 */
	public function accept(Reminder $Reminder): void
	{
		$this->setStatus($Reminder, Reminder::STATUS_ACCEPTED);
	}

	/**
	 * @throws SaveModelException
	 */
	public function done(Reminder $Reminder): void
	{
		$this->setStatus($Reminder, Reminder::STATUS_DONE);
	}

	/**
	 * @throws SaveModelException
	 */
	public function impossible(Reminder $Reminder): void
	{
		$this->setStatus($Reminder, Reminder::STATUS_IMPOSSIBLE);
	}

	/**
	 * @throws SaveModelException
	 */
	public function changeStatus(Reminder $Reminder, int $status): void
	{
		switch ($status) {
			case Reminder::STATUS_DONE:
				$this->done($Reminder);
				break;
			case Reminder::STATUS_ACCEPTED:
				$this->accept($Reminder);
				break;
			case Reminder::STATUS_IMPOSSIBLE:
				$this->impossible($Reminder);
				break;
			default:
				throw new UnexpectedValueException('Unexpected status');
		};
	}

	/**
	 * @throws SaveModelException
	 */
	private function setStatus(Reminder $Reminder, int $status): void
	{
		$Reminder->status = $status;
		$Reminder->saveOrThrow();
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Reminder $Reminder): void
	{
		$Reminder->delete();
	}
}