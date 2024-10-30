<?php

declare(strict_types=1);

namespace app\usecases\User;

use app\dto\User\CreateUserDto;
use app\dto\User\CreateUserProfileDto;
use app\dto\User\UpdateUserDto;
use app\exceptions\ValidationErrorHttpException;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\UploadFile;
use app\models\User;
use Throwable;
use yii\base\Security;
use yii\db\ActiveRecord;

class UserService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private UserProfileService           $userProfileService;
	private Security                     $security;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		UserProfileService $userProfileService,
		Security $security
	)
	{
		$this->security            = $security;
		$this->transactionBeginner = $transactionBeginner;
		$this->userProfileService  = $userProfileService;
	}

	/**
	 * @param CreateUserDto        $createUserDto
	 * @param CreateUserProfileDto $userProfileDto
	 * @param UploadFile           $uploadMedia
	 *
	 * @return User|array|ActiveRecord|null
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidationErrorHttpException
	 */
	public function create(CreateUserDto $createUserDto, CreateUserProfileDto $userProfileDto, UploadFile $uploadMedia)
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model = new User([
				'username'       => $createUserDto->username,
				'email'          => $createUserDto->email,
				'email_username' => $createUserDto->email_username,
				'email_password' => $createUserDto->email_password,
				'role'           => $createUserDto->role,
				'password_hash'  => $this->security->generatePasswordHash($createUserDto->password)
			]);

			$model->saveOrThrow();

			$this->userProfileService->create($model->id, $userProfileDto, $uploadMedia);

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/** Updates a user.
	 *
	 * @param User                 $model          The model to update.
	 * @param UpdateUserDto        $dto            The data to update the model with.
	 * @param CreateUserProfileDto $userProfileDto The data to update the user profile with.
	 * @param UploadFile           $uploadMedia    The uploaded media.
	 *
	 * @return User|ActiveRecord|null The updated model.
	 * @throws SaveModelException If the model cannot be saved.
	 * @throws Throwable If the transaction cannot be committed.
	 * @throws ValidationErrorHttpException If the model cannot be validated.
	 */
	public function update(User $model, UpdateUserDto $dto, CreateUserProfileDto $userProfileDto, UploadFile $uploadMedia)
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model->load([
				'email'          => $dto->email,
				'email_username' => $dto->email_username,
				'role'           => $dto->role
			]);

			if ($dto->email_password !== null) {
				$model->email_password = $dto->email_password;
			}

			if ($dto->password !== null) {
				$model->password_hash = $this->security->generatePasswordHash($dto->password);
			}

			$model->saveOrThrow();

			$userProfileModel = $model->userProfile;

			$this->userProfileService->update($userProfileModel, $userProfileDto, $uploadMedia);

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @param User $model The model to delete.
	 *
	 * @return void
	 * @throws SaveModelException If the model cannot be saved.
	 */
	public function delete(User $model): void
	{
		$model->status = User::STATUS_DELETED;

		$model->saveOrThrow();
	}
}