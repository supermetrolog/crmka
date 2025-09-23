<?php

declare(strict_types=1);

namespace app\usecases\Notification;

use app\dto\UserNotification\CreateUserNotificationDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Notification\UserNotification;
use app\models\User\User;
use yii\base\ErrorException;

class UserNotificationService
{
	protected TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateUserNotificationDto $dto): UserNotification
	{
		$model = new UserNotification();

		$model->mailing_id  = $dto->mailing_id;
		$model->user_id     = $dto->user_id;
		$model->notified_at = $dto->notified_at ? $dto->notified_at->format('Y-m-d H:i:s') : null;
		$model->viewed_at   = $dto->viewed_at ? $dto->viewed_at->format('Y-m-d H:i:s') : null;
		$model->template_id = $dto->template->id ?? null;

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function viewed(UserNotification $notification): void
	{
		if (!is_null($notification->viewed_at)) {
			return;
		}

		$notification->viewed_at = DateTimeHelper::nowf();

		$notification->saveOrThrow();
	}

	/**
	 * @throws SaveModelException
	 */
	public function acted(UserNotification $notification): void
	{
		if (!is_null($notification->acted_at)) {
			return;
		}

		$notification->acted_at = DateTimeHelper::nowf();

		$notification->saveOrThrow();
	}

	/**
	 * @throws ErrorException
	 */
	public function actedAllForUser(User $user): int
	{
		$query = UserNotification::find()->byUserId($user->id)->notActed()->notExpired();

		return $this->transactionBeginner->run(function () use ($query) {
			$count = 0;

			/** @var UserNotification $notification */
			foreach ($query->each() as $notification) {
				$this->acted($notification);

				$count++;
			}

			return $count;
		});
	}
}