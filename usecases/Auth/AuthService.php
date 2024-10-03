<?php

namespace app\usecases\Auth;

use app\dto\Auth\AuthLoginDto;
use app\dto\Auth\AuthResultDto;
use app\dto\Auth\AuthUserAgentDto;
use app\exceptions\InvalidBearerTokenException;
use app\exceptions\InvalidPasswordException;
use app\helpers\DateIntervalHelper;
use app\helpers\DateTimeHelper;
use app\helpers\StringHelper;
use app\kernel\common\models\exceptions\ModelNotFoundException;
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
	 * @return AuthResultDto The user and the access token.
	 * @throws ErrorException If the access token cannot be generated.
	 * @throws Exception If the username or password is invalid.
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 * @throws InvalidPasswordException
	 */
	public function login(AuthLoginDto $dto, AuthUserAgentDto $userAgentDto): AuthResultDto
	{
		$user = User::find()->byUsername($dto->username)->oneOrThrow();

		if (!$this->validatePassword($user, $dto->password)) {
			throw new InvalidPasswordException();
		}

		$accessToken = $this->generateAccessToken($user, $userAgentDto);

		return new AuthResultDto([
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
			'ip'           => $userAgentDto->ip,
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
		return UserAccessToken::find()->valid()->byToken($token)->exists();
	}

	/**
	 * Logs out the user by deleting the access token.
	 *
	 * @param string $token The access token.
	 *
	 * @return void
	 * @throws InvalidBearerTokenException
	 * @throws StaleObjectException If the access token cannot be deleted.
	 * @throws Throwable If the access token cannot be deleted.
	 * @throws ModelNotFoundException
	 */
	public function logout(string $token)
	{
		$userAccessToken = UserAccessToken::find()->byToken($token)->oneOrThrow();
		$userAccessToken->delete();
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