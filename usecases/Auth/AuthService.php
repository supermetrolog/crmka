<?php

namespace app\usecases\Auth;

use app\dto\Auth\AuthLoginDto;
use app\dto\Auth\AuthResultDto;
use app\dto\Auth\AuthUserAgentDto;
use app\dto\User\UserAccessTokenDto;
use app\dto\User\UserActivityDto;
use app\exceptions\InvalidPasswordException;
use app\exceptions\services\RestrictedUserIpAccessException;
use app\exceptions\services\UserHasInactiveStatusException;
use app\helpers\ArrayHelper;
use app\helpers\DateIntervalHelper;
use app\helpers\DateTimeHelper;
use app\helpers\StringHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\User\User;
use app\models\User\UserAccessToken;
use app\usecases\User\UserAccessTokenService;
use app\usecases\User\UserService;
use Throwable;
use yii\base\Exception;
use yii\base\Security;
use yii\db\StaleObjectException;

class AuthService
{
	public array $allowedOfficeIps = [];

	private Security                     $security;
	private UserService                  $userService;
	private UserAccessTokenService       $userAccessTokenService;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		Security $security,
		UserService $userService,
		UserAccessTokenService $userAccessTokenService,
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->security               = $security;
		$this->userService            = $userService;
		$this->userAccessTokenService = $userAccessTokenService;
		$this->transactionBeginner    = $transactionBeginner;
	}

	/**
	 * @param AuthLoginDto     $dto
	 * @param AuthUserAgentDto $userAgentDto
	 *
	 * @return AuthResultDto
	 * @throws Exception
	 * @throws InvalidPasswordException
	 * @throws ModelNotFoundException
	 * @throws SaveModelException|Throwable
	 */
	public function login(AuthLoginDto $dto, AuthUserAgentDto $userAgentDto): AuthResultDto
	{
		$user = $this->userService->getByUsername($dto->username);

		if ($user->isInactive()) {
			throw new UserHasInactiveStatusException();
		}

		if (!$this->validatePassword($user, $dto->password)) {
			throw new InvalidPasswordException();
		}

		if ($user->isIpAccessRestricted() && !$this->isOfficeIpAllowed($userAgentDto->ip)) {
			throw new RestrictedUserIpAccessException();
		}

		$tx = $this->transactionBeginner->begin();

		try {
			$userAccessToken = $this->generateAccessToken($user, $userAgentDto);

			$this->userService->updateActivity($user, new UserActivityDto([
				'user_id'    => $user->id,
				'ip'         => $userAgentDto->ip,
				'user_agent' => $userAgentDto->agent,
				'last_page'  => null
			]));

			$tx->commit();

			return new AuthResultDto([
				'user'          => $user,
				'accessToken'   => $userAccessToken->access_token,
				'accessTokenId' => $userAccessToken->id
			]);
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
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
	public function logout(string $token): void
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

	public function isOfficeIpAllowed(string $ip): bool
	{
		if (empty($this->allowedOfficeIps)) {
			return true;
		}

		return ArrayHelper::includes($this->allowedOfficeIps, $ip);
	}
}