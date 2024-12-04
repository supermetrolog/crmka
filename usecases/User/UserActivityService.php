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
	public function create(UserActivityDto $dto, string $startedAt): UserActivity
	{
		$model = new UserActivity([
			'user_id'          => $dto->user_id,
			'ip'               => $dto->ip,
			'user_agent'       => $dto->user_agent,
			'last_page'        => $dto->last_page,
			'started_at'       => $startedAt,
			'last_activity_at' => $startedAt
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(UserActivity $model, UserActivityDto $dto, string $lastActivityAt): UserActivity
	{
		$model->load([
			'ip'               => $dto->ip,
			'user_agent'       => $dto->user_agent,
			'last_page'        => $dto->last_page,
			'last_activity_at' => $lastActivityAt
		]);

		$model->saveOrThrow();

		return $model;
	}

	public function recreate(UserActivity $oldModel, UserActivityDto $dto, string $lastActivityAt, string $endedAt): UserActivity
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->update($oldModel, $dto, $endedAt);

			$newModel = $this->create($dto, $lastActivityAt);

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
		$currentTime = DateTimeHelper::unix();

		$lastActivity = $this->repository->findLastActivityByUserId($dto->user_id);

		if ($this->shouldCreateNewActivity($lastActivity, $currentTime)) {
			return $this->create($dto, DateTimeHelper::fromUnixf($currentTime));
		}

		if ($this->shouldReCreateActivity($lastActivity, $currentTime)) {
			return $this->recreate(
				$lastActivity,
				$dto,
				DateTimeHelper::fromUnixf($currentTime),
				DateTimeHelper::makef($lastActivity->last_activity_at, 'Y-m-d 23:59:59')
			);
		}

		return $this->update($lastActivity, $dto, DateTimeHelper::fromUnixf($currentTime));

	}

	/**
	 * @throws Exception
	 */
	private function shouldCreateNewActivity(?UserActivity $lastActivity, int $currentTime): bool
	{
		if (is_null($lastActivity)) {
			return true;
		}

		$lastActivityUnix = DateTimeHelper::makeUnix($lastActivity->last_activity_at);

		return $currentTime - $lastActivityUnix > self::ACTIVITY_TIMEOUT_MINUTES * 60;
	}

	/**
	 * @throws Exception
	 */
	private function shouldReCreateActivity(UserActivity $lastActivity, int $currentTime): bool
	{
		return !DateTimeHelper::isSameDate(
			DateTimeHelper::make($lastActivity->started_at),
			DateTimeHelper::fromUnix($currentTime)
		);
	}

	public function getTotalOnlineTime(User $user, string $startDate, string $endDate): int
	{
		// TODO: Implement calculating total online time

		return 0;
	}
}