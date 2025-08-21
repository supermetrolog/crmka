<?php

declare(strict_types=1);

namespace app\usecases\User;

use app\dto\User\CreateUserProfileDto;
use app\exceptions\ValidationErrorHttpException;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\miniModels\UserProfileEmail;
use app\models\miniModels\UserProfilePhone;
use app\models\UploadFile;
use app\models\UserProfile;
use Throwable;

class UserProfileService
{
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @param int                  $userId
	 * @param CreateUserProfileDto $userProfileDto
	 * @param UploadFile           $uploadMedia
	 *
	 * @return UserProfile
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidationErrorHttpException
	 */
	public function create(int $userId, CreateUserProfileDto $userProfileDto, UploadFile $uploadMedia): UserProfile
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model = new UserProfile([
				'user_id'     => $userId,
				'first_name'  => $userProfileDto->first_name,
				'middle_name' => $userProfileDto->middle_name,
				'last_name'   => $userProfileDto->last_name,
				'caller_id'   => $userProfileDto->caller_id,
				'gender'      => $userProfileDto->gender
			]);

			$model = $model->uploadFiles($uploadMedia, $model);
			$model->saveOrThrow();

			$model->createManyMiniModels([
				UserProfileEmail::class => $userProfileDto->emails,
				UserProfilePhone::class => $userProfileDto->phones
			]);

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @param UserProfile          $model
	 * @param CreateUserProfileDto $userProfileDto
	 * @param UploadFile           $uploadMedia
	 *
	 * @return UserProfile
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidationErrorHttpException
	 */
	public function update(UserProfile $model, CreateUserProfileDto $userProfileDto, UploadFile $uploadMedia): UserProfile
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model->load([
				'first_name'  => $userProfileDto->first_name,
				'middle_name' => $userProfileDto->middle_name,
				'last_name'   => $userProfileDto->last_name,
				'caller_id'   => $userProfileDto->caller_id,
				'gender'      => $userProfileDto->gender
			]);

			$model = $model->uploadFiles($uploadMedia, $model);
			$model->saveOrThrow();

			$model->updateManyMiniModels([
				UserProfileEmail::class => $userProfileDto->emails,
				UserProfilePhone::class => $userProfileDto->phones
			]);

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}
}