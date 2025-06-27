<?php

declare(strict_types=1);

namespace app\usecases\UserTour;

use app\dto\UserTour\UserTourViewDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\UserTourStatus;
use app\models\UserTourView;
use app\repositories\UserTourStatusRepository;
use Throwable;

class UserTourService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private UserTourStatusRepository     $tourStatusRepository;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		UserTourStatusRepository $tourStatusRepository
	)
	{
		$this->transactionBeginner  = $transactionBeginner;
		$this->tourStatusRepository = $tourStatusRepository;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function markAsViewed(UserTourViewDto $dto): UserTourView
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$tourView = $this->createTourView($dto);

			$this->actualizeTourStatus($tourView);

			$tx->commit();

			return $tourView;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 */
	private function createTourView(UserTourViewDto $dto): UserTourView
	{
		$model = new UserTourView([
			'tour_id'      => $dto->tour_id,
			'user_id'      => $dto->user->id,
			'steps_viewed' => $dto->steps_viewed,
			'steps_total'  => $dto->steps_total,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	private function actualizeTourStatus(UserTourView $tourView): UserTourStatus
	{
		$tourStatus = $this->tourStatusRepository->findOneByUserIdAndTourId($tourView->user_id, $tourView->tour_id);

		if (!$tourStatus) {
			return $this->createTourStatus($tourView);
		}

		if (!$tourStatus->viewed) {
			$tourStatus->viewed = true;
			$tourStatus->saveOrThrow();

			return $tourStatus;
		}

		return $tourStatus;
	}

	/**
	 * @throws SaveModelException
	 */
	private function createTourStatus(UserTourView $tourView): UserTourStatus
	{
		$tourStatus = new UserTourStatus([
			'tour_id'  => $tourView->tour_id,
			'user_id'  => $tourView->user_id,
			'viewed'   => true,
			'reset_at' => null
		]);

		$tourStatus->saveOrThrow();

		return $tourStatus;
	}

	/**
	 * @throws SaveModelException
	 */
	public function reset(UserTourStatus $tourStatus): UserTourStatus
	{
		$tourStatus->viewed   = false;
		$tourStatus->reset_at = DateTimeHelper::nowf();

		$tourStatus->saveOrThrow();

		return $tourStatus;
	}
}