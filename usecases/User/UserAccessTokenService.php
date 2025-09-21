<?php

declare(strict_types=1);

namespace app\usecases\User;

use app\dto\User\UserAccessTokenDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\User\UserAccessToken;
use Throwable;
use yii\db\StaleObjectException;

class UserAccessTokenService
{
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @param UserAccessTokenDto $dto
	 *
	 * @return UserAccessToken
	 * @throws SaveModelException
	 */
	public function create(UserAccessTokenDto $dto): UserAccessToken
	{
		$model = new UserAccessToken([
			'user_id'      => $dto->user_id,
			'access_token' => $dto->access_token,
			'expires_at'   => $dto->expires_at,
			'ip'           => $dto->ip,
			'user_agent'   => $dto->user_agent
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @param UserAccessToken $model
	 *
	 * @return void
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(UserAccessToken $model): void
	{
		$model->delete();
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function deleteAllByUserId(int $userId)
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$models = UserAccessToken::find()
			                         ->valid()
			                         ->byUserId($userId)
			                         ->all();

			foreach ($models as $model) {
				$this->delete($model);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	public function deleteByUserIdExcludingToken(int $userId, string $excludeToken)
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$models = UserAccessToken::find()
			                         ->valid()
			                         ->byUserId($userId)
			                         ->excludeToken($excludeToken)
			                         ->all();

			foreach ($models as $model) {
				$this->delete($model);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

}