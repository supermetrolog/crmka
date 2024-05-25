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
	public function update(Reminder $reminder, UpdateReminderDto $dto): Reminder
	{
		$reminder->load([
			'user_id'   => $dto->user->id,
			'message'   => $dto->message,
			'status'    => $dto->status,
			'notify_at' => $dto->notify_at ? $dto->notify_at->format('Y-m-d H:i:s') : null,
		]);

		$reminder->saveOrThrow();

		return $reminder;
	}

	/**
	 * @throws SaveModelException
	 */
	public function accept(Reminder $reminder): void
	{
		$this->setStatus($reminder, Reminder::STATUS_ACCEPTED);
	}

	/**
	 * @throws SaveModelException
	 */
	public function done(Reminder $reminder): void
	{
		$this->setStatus($reminder, Reminder::STATUS_DONE);
	}

	/**
	 * @throws SaveModelException
	 */
	public function impossible(Reminder $reminder): void
	{
		$this->setStatus($reminder, Reminder::STATUS_IMPOSSIBLE);
	}

	/**
	 * @throws SaveModelException
	 */
	public function later(Reminder $reminder): void
	{
		$this->setStatus($reminder, Reminder::STATUS_LATER);
	}
	
	/**
	 * @throws SaveModelException
	 */
	public function changeStatus(Reminder $reminder, int $status): void
	{
		switch ($status) {
			case Reminder::STATUS_DONE:
				$this->done($reminder);
				break;
			case Reminder::STATUS_ACCEPTED:
				$this->accept($reminder);
				break;
			case Reminder::STATUS_IMPOSSIBLE:
				$this->impossible($reminder);
				break;
			case Reminder::STATUS_LATER:
				$this->impossible($reminder);
				break;
			default:
				throw new UnexpectedValueException('Unexpected status');
		};
	}

	/**
	 * @throws SaveModelException
	 */
	private function setStatus(Reminder $reminder, int $status): void
	{
		$reminder->status = $status;
		$reminder->saveOrThrow();
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Reminder $reminder): void
	{
		$reminder->delete();
	}
}