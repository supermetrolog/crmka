<?php

namespace app\models\forms;

use app\dto\Auth\AuthLoginDto;
use app\kernel\common\models\Form\Form;
use app\models\User;
use app\usecases\Auth\AuthService;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read AuthLoginDto $dto
 */
class LoginForm extends Form
{
	public string $username;
	public string $password;

	private AuthService $authService;

	public function __construct(AuthService $authService, $config = [])
	{
		$this->authService = $authService;
		parent::__construct($config);
	}

	public function rules(): array
	{
		return [
			[['username', 'password'], 'string'],
			[['username', 'password'], 'required'],
			['password', 'validatePassword']
		];
	}

	/**
	 * @return AuthLoginDto
	 */
	public function getDto(): AuthLoginDto
	{
		return new AuthLoginDto([
			'username' => $this->username,
			'password' => $this->password
		]);
	}

	public function attributeLabels(): array
	{
		return [
			'username' => 'Логин',
			'password' => 'Пароль'
		];
	}

	/**
	 * Validates the password.
	 * This method serves as the inline validation for password.
	 *
	 * @param string $attribute The attribute currently being validated
	 */
	public function validatePassword(string $attribute)
	{
		if (!$this->hasErrors()) {
			$user = User::find()->byUsername($this->username)->one();

			if ($user === null || !$this->authService->validatePassword($user, $this->password)) {
				$this->addError($attribute, 'Неверный логин или пароль.');
			}
		}
	}
}
