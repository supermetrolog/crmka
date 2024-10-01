<?php

namespace app\models\forms;

use app\dto\Authentication\AuthenticationLoginDto;
use app\kernel\common\models\Form\Form;
use app\models\User;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read AuthenticationLoginDto $dto
 */
class LoginForm extends Form
{
	public string $username;
	public string $password;

	public function rules(): array
	{
		return [
			[['username', 'password'], 'string'],
			[['username', 'password'], 'required'],
			['password', 'validatePassword']
		];
	}

	/**
	 * @return AuthenticationLoginDto
	 */
	public function getDto(): AuthenticationLoginDto
	{
		return new AuthenticationLoginDto([
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
			\Yii::debug($this->username);
			$user = User::findByUsername($this->username);

			if ($user === null || !$user->validatePassword($this->password)) {
				$this->addError($attribute, 'Неверный логин или пароль.');
			}
		}
	}
}
