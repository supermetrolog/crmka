<?php

namespace app\usecases\Auth;

use app\dto\Auth\AuthLoginDto;
use app\dto\Auth\AuthResponseDto;
use app\dto\Auth\AuthUserAgentDto;
use app\helpers\DateIntervalHelper;
use app\helpers\DateTimeHelper;
use app\helpers\StringHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\User;
use app\models\UserAccessToken;
use Throwable;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\Security;
use yii\db\StaleObjectException;

class AuthService
{

	private Security $security;


	public function __construct(
		Security $security
	)
	{
		$this->security = $security;
	}

	/**
	 * Authenticates a user by username and password.
	 *
	 * @return AuthResponseDto The user and the access token.
	 * @throws ErrorException If the access token cannot be generated.
	 * @throws Exception If the username or password is invalid.
	 * @throws SaveModelException
	 */
	public function login(AuthLoginDto $dto, AuthUserAgentDto $userAgentDto): AuthResponseDto
	{
		$user = User::find()->byUsername($dto->username)->one();

		if (!$user || !$this->validatePassword($user, $dto->password)) {
			throw new Exception('Неверное имя пользователя или пароль.');
		}

		$accessToken = $this->generateAccessToken($user, $userAgentDto);

		return new AuthResponseDto([
			'user'        => $user,
			'accessToken' => $accessToken,
		]);
	}

	/**
	 * Generates an access token for the authenticated user.
	 *
	 * @param User $user The authenticated user.
	 *
	 * @return string The generated access token.
	 * @throws Exception If the access token cannot be saved.
	 * @throws ErrorException If the access token cannot be generated.
	 * @throws SaveModelException
	 */
	private function generateAccessToken(User $user, AuthUserAgentDto $userAgentDto): string
	{
		$token    = $this->security->generateRandomString() . '_' . time();
		$dateTime = DateTimeHelper::now();
		$dateTime->add(DateIntervalHelper::days(UserAccessToken::EXPIRES_IN_DAYS));
		$expiresAt = $dateTime->format('Y-m-d H:i:s');

		$userAccessToken = new UserAccessToken([
			'user_id'      => $user->id,
			'access_token' => $token,
			'expires_at'   => $expiresAt,
			'ip'           => $userAgentDto->IP,
			'user_agent'   => StringHelper::truncate($userAgentDto->agent, 1024)
		]);

		$userAccessToken->saveOrThrow();

		return $token;
	}

	/**
	 * Validates the access token.
	 *
	 * @param string $token The access token.
	 *
	 * @return bool Whether the access token is valid.
	 */
	public function validateAccessToken(string $token): bool
	{
		$userAccessToken = UserAccessToken::find()->notDeleted()->byToken($token)->one();

		if (!$userAccessToken || !$userAccessToken->isValid()) {
			return false;
		}

		return true;
	}

	/**
	 * Logs out the user by deleting the access token.
	 *
	 * @param string $token The access token.
	 *
	 * @return void
	 * @throws Exception If the access token is invalid.
	 * @throws StaleObjectException If the access token cannot be deleted.
	 * @throws Throwable If the access token cannot be deleted.
	 */
	public function logout(string $token)
	{
		$tokenIsValid = $this->validateAccessToken($token);

		if ($tokenIsValid) {
			$userAccessToken = UserAccessToken::find()->onlyValid()->byToken($token)->one();
			$userAccessToken->delete();
		} else {
			throw new Exception('Токен авторизации уже не является актуальным.');
		}
	}

	/**
	 * @param User   $user
	 * @param string $password
	 *
	 * @return bool Whether the password is valid.
	 */
	public function validatePassword(User $user, string $password): bool
	{
		return $this->security->validatePassword($password, $user->password_hash);
	}
}