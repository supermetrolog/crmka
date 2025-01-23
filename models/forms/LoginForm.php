<?php

namespace app\models\forms;

use app\dto\Auth\AuthLoginDto;
use app\kernel\common\models\Form\Form;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read AuthLoginDto $dto
 */
class LoginForm extends Form
{
	public $username;
	public $password;

	public function rules(): array
	{
		return [
			[['username', 'password'], 'string'],
			[['username', 'password'], 'required']
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
}
