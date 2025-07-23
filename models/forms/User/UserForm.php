<?php

declare(strict_types=1);

namespace app\models\forms\User;

use app\dto\User\CreateUserDto;
use app\dto\User\UpdateUserDto;
use app\kernel\common\models\Form\Form;
use app\models\User;
use Exception;

class UserForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $username;
	public $email;
	public $email_username;
	public $email_password;
	public $role;
	public $password;
	public $restrict_ip_login;

	public function rules(): array
	{
		return [
			[['username', 'role'], 'required'],
			['password', 'required', 'on' => self::SCENARIO_CREATE],
			['password', 'string', 'min' => 8],
			['username', 'string', 'min' => 4, 'max' => 32],
			[['email', 'email_password', 'email_username'], 'string', 'max' => 255],
			['username', 'validateUsername'],
			['restrict_ip_login', 'boolean'],
			['role', 'integer'],
			['role', 'in', 'range' => User::getRoles()]
		];
	}

	/**
	 * Validates the password.
	 * This method serves as the inline validation for password.
	 *
	 * @param string $attribute the attribute currently being validated
	 */
	public function validateUsername(string $attribute): void
	{
		if (!$this->hasErrors()) {
			$userExist = User::find()->andWhere(['username' => $this->$attribute])->exists();

			if ($userExist) {
				$this->addError($attribute, 'Пользователь с таким username уже существует.');
			}
		}
	}

	public function attributeLabels(): array
	{
		return [
			'username'          => 'Логин',
			'password'          => 'Пароль',
			'role'              => 'Роль',
			'email'             => 'Email',
			'email_username'    => 'Логин от почты',
			'email_password'    => 'Пароль от почты',
			'restrict_ip_login' => 'Запрет на дистанционный вход',
		];
	}

	public function scenarios(): array
	{
		$common = [
			'email',
			'email_username',
			'role',
			'password',
			'email_password',
			'restrict_ip_login'
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'username'],
			self::SCENARIO_UPDATE => $common,
		];
	}

	/**
	 * @return CreateUserDto|UpdateUserDto
	 * @throws Exception
	 */
	public function getDto()
	{
		if ($this->getScenario() === self::SCENARIO_CREATE) {
			return new CreateUserDto([
				'username'          => $this->username,
				'email'             => $this->email,
				'email_username'    => $this->email_username,
				'email_password'    => $this->email_password,
				'role'              => $this->role,
				'password'          => $this->password,
				'restrict_ip_login' => $this->restrict_ip_login
			]);
		}

		return new UpdateUserDto([
			'email'             => $this->email,
			'email_username'    => $this->email_username,
			'email_password'    => $this->email_password,
			'role'              => $this->role,
			'password'          => $this->password,
			'restrict_ip_login' => $this->restrict_ip_login
		]);
	}
}