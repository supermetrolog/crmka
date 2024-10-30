<?php

declare(strict_types=1);

namespace app\usecases\User;

use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\User;
use Throwable;
use yii\db\StaleObjectException;

class UserWithAccessTokenService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private UserService                  $userService;
	private UserAccessTokenService       $userAccessTokenService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		UserService $userService,
		UserAccessTokenService $userAccessTokenService
	)
	{
		$this->transactionBeginner    = $transactionBeginner;
		$this->userService            = $userService;
		$this->userAccessTokenService = $userAccessTokenService;
	}

	/**
	 * @throws SaveModelException
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(User $model): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->userService->delete($model);
			$this->userAccessTokenService->deleteAllByUserId($model->id);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function archive(User $model): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->userService->archive($model);
			$this->userAccessTokenService->deleteAllByUserId($model->id);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}
}