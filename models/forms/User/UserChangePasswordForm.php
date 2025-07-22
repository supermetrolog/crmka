<?php

declare(strict_types=1);

namespace app\models\forms\User;

use app\dto\User\ChangeUserPasswordDto;
use app\kernel\common\models\Form\Form;

class UserChangePasswordForm extends Form
{
	public $current_password;
	public $new_password;

	public function rules(): array
	{
		return [
			[['current_password', 'new_password'], 'required'],
			[['new_password'], 'string', 'min' => 8]
		];
	}

	public function attributeLabels(): array
	{
		return [
			'current_password' => 'Текущий пароль',
			'new_password'     => 'Новый пароль'
		];
	}

	public function getDto(): ChangeUserPasswordDto
	{
		return new ChangeUserPasswordDto([
			'currentPassword' => $this->current_password,
			'newPassword'     => $this->new_password
		]);
	}
}