<?php

declare(strict_types=1);

namespace app\usecases\User;

use app\dto\User\UserActivityDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\User;
use app\models\UserActivity;
use app\repositories\UserActivityRepository;
use DateTimeInterface;
use Exception;
use Throwable;

class UserActivityService
{
	public const ACTIVITY_TIMEOUT_MINUTES = 6;

	private UserActivityRepository       $repository;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		UserActivityRepository $repository,
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->repository          = $repository;
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @throws SaveModelException
	 */
	private function create(UserActivityDto $dto, DateTimeInterface $startedAt, ?DateTimeInterface $lastActivityAt = null): UserActivity
	{
		$model = new UserActivity([
			'user_id'          => $dto->user_id,
			'ip'               => $dto->ip,
			'user_agent'       => $dto->user_agent,
			'last_page'        => $dto->last_page,
			'started_at'       => DateTimeHelper::format($startedAt),
			'last_activity_at' => DateTimeHelper::format($lastActivityAt ?? $startedAt)
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 */
	private function update(UserActivity $model, UserActivityDto $dto, DateTimeInterface $lastActivityAt): UserActivity
	{
		$model->load([
			'ip'               => $dto->ip,
			'user_agent'       => $dto->user_agent,
			'last_page'        => $dto->last_page,
			'last_activity_at' => DateTimeHelper::format($lastActivityAt)
		]);

		$model->saveOrThrow();

		return $model;
	}

	private function recreate(UserActivity $oldModel, UserActivityDto $dto, DateTimeInterface $lastActivityAt): UserActivity
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->update($oldModel, $dto, DateTimeHelper::getDayEndTime($oldModel->getStartedAt()));

			$newModel = $this->create($dto, DateTimeHelper::getDayStartTime($lastActivityAt), $lastActivityAt);

			$tx->commit();

			return $newModel;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function track(UserActivityDto $dto): UserActivity
	{
		$currentTime = DateTimeHelper::now();

		$lastActivity = $this->repository->findLastActivityByUserId($dto->user_id);

		if ($this->shouldCreateNewActivity($lastActivity, $currentTime)) {
			return $this->create($dto, $currentTime);
		}

		if ($this->shouldReCreateActivity($lastActivity, $currentTime)) {
			return $this->recreate($lastActivity, $dto, $currentTime);
		}

		return $this->update($lastActivity, $dto, $currentTime);
	}

	/**
	 * @throws Exception
	 */
	private function shouldCreateNewActivity(?UserActivity $lastActivity, DateTimeInterface $currentTime): bool
	{
		if (is_null($lastActivity)) {
			return true;
		}

		$diff = DateTimeHelper::diffInMinutes($currentTime, $lastActivity->getLastActivityAt());

		return $diff > self::ACTIVITY_TIMEOUT_MINUTES;
	}

	/**
	 * @throws Exception
	 */
	private function shouldReCreateActivity(UserActivity $lastActivity, DateTimeInterface $currentTime): bool
	{
		return !DateTimeHelper::isSameDate(
			$lastActivity->getStartedAt(),
			$currentTime
		);
	}

	/**
	 * @throws Exception
	 */
	public function getTotalOnlineTime(User $user, string $startDate, string $endDate): int
	{
		$activities = UserActivity::find()->byUserId($user->id)->between($startDate, $endDate)->all();

		$totalTime = 0;

		foreach ($activities as $activity) {
			$totalTime += DateTimeHelper::diffInMinutes(
				$activity->getLastActivityAt(),
				$activity->getStartedAt()
			);
		}

		return $totalTime;
	}
}