<?php

namespace app\usecases\Authentication;

use app\dto\Authentication\AuthenticationResponseDto;
use app\helpers\DateIntervalHelper;
use app\helpers\DateTimeHelper;
use app\models\User;
use app\models\UserAccessToken;
use Throwable;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\db\StaleObjectException;

class AuthenticationService
{
	/**
	 * Authenticates a user by username and password.
	 *
	 * @param string $username The user's username.
	 * @param string $password The user's password.
	 *
	 * @return AuthenticationResponseDto The user and the access token.
	 * @throws ErrorException If the access token cannot be generated.
	 * @throws Exception If the username or password is invalid.
	 */
	public function authenticate(string $username, string $password): AuthenticationResponseDto
	{
		$user = User::findByUsername($username);

		if (!$user || !$user->validatePassword($password)) {
			throw new Exception('Неверное имя пользователя или пароль.');
		}

		$accessToken = $this->generateAccessToken($user);

		return new AuthenticationResponseDto([
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
	 */
	private function generateAccessToken(User $user): string
	{
		$token     = Yii::$app->security->generateRandomString() . '_' . time();
		$userIP    = Yii::$app->request->getUserIP();
		$userAgent = Yii::$app->request->getUserAgent();

		$dateTime = DateTimeHelper::now();
		$dateTime->add(DateIntervalHelper::days(UserAccessToken::EXPIRES_IN_DAYS));
		$expiresAt = $dateTime->format('Y-m-d H:i:s');

		$userAccessToken = new UserAccessToken([
			'user_id'      => $user->id,
			'access_token' => $token,
			'expires_at'   => $expiresAt,
			'ip'           => $userIP,
			'user_agent'   => $userAgent
		]);

		if (!$userAccessToken->save()) {
			throw new Exception('Ошибка при генерации токена авторизации.');
		}

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
		$userAccessToken = UserAccessToken::findValid()->byToken($token)->one();

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
			$userAccessToken = UserAccessToken::findValid()->byToken($token)->one();
			$userAccessToken->delete();
		} else {
			throw new Exception('Токен авторизации уже не является актуальным.');
		}
	}
}