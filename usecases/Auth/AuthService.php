<?php

namespace app\usecases\Auth;

use app\dto\Auth\AuthLoginDto;
use app\dto\Auth\AuthResultDto;
use app\dto\Auth\AuthUserAgentDto;
use app\dto\User\UserAccessTokenDto;
use app\exceptions\InvalidPasswordException;
use app\helpers\DateIntervalHelper;
use app\helpers\DateTimeHelper;
use app\helpers\StringHelper;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\User;
use app\models\UserAccessToken;
use app\usecases\User\UserAccessTokenService;
use Throwable;
use yii\base\Exception;
use yii\base\Security;
use yii\db\StaleObjectException;

class AuthService
{

	private Security               $security;
	private UserAccessTokenService $userAccessTokenService;

	public function __construct(
		Security $security,
		UserAccessTokenService $userAccessTokenService
	)
	{
		$this->security               = $security;
		$this->userAccessTokenService = $userAccessTokenService;
	}

	/**
	 * @param AuthLoginDto     $dto
	 * @param AuthUserAgentDto $userAgentDto
	 *
	 * @return AuthResultDto
	 * @throws Exception
	 * @throws InvalidPasswordException
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function login(AuthLoginDto $dto, AuthUserAgentDto $userAgentDto): AuthResultDto
	{
		$user = User::find()->byUsername($dto->username)->oneOrThrow();

		if (!$this->validatePassword($user, $dto->password)) {
			throw new InvalidPasswordException();
		}

		$userAccessToken = $this->generateAccessToken($user, $userAgentDto);

		return new AuthResultDto([
			'user'          => $user,
			'accessToken'   => $userAccessToken->access_token,
			'accessTokenId' => $userAccessToken->id
		]);
	}

	/**
	 * @param User             $user
	 * @param AuthUserAgentDto $userAgentDto
	 *
	 * @return UserAccessToken
	 * @throws Exception
	 * @throws SaveModelException
	 */
	private function generateAccessToken(User $user, AuthUserAgentDto $userAgentDto): UserAccessToken
	{
		$token    = $this->security->generateRandomString() . '_' . time();
		$dateTime = DateTimeHelper::now();
		$dateTime->add(DateIntervalHelper::days(UserAccessToken::EXPIRES_IN_DAYS));
		$expiresAt = $dateTime->format('Y-m-d H:i:s');

		$userAccessToken = $this->userAccessTokenService->create(new UserAccessTokenDto([
			'user_id'      => $user->id,
			'access_token' => $token,
			'expires_at'   => $expiresAt,
			'ip'           => $userAgentDto->ip,
			'user_agent'   => StringHelper::truncate($userAgentDto->agent, 1024)
		]));

		return $userAccessToken;
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
	 * @throws ModelNotFoundException
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function logout(string $token)
	{
		$userAccessToken = UserAccessToken::find()->byToken($token)->oneOrThrow();
		$this->userAccessTokenService->delete($userAccessToken);
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