<?php

declare(strict_types=1);

namespace app\models\forms\User;

use app\dto\User\CreateUserProfileDto;
use app\kernel\common\models\Form\Form;
use app\models\UserProfile;
use Exception;

class UserProfileForm extends Form
{
	public string  $first_name;
	public string  $middle_name;
	public ?string $last_name = null;
	public ?string $caller_id = null;
	public array   $phones    = [];
	public array   $emails    = [];

	public function rules(): array
	{
		return [
			[['first_name', 'middle_name'], 'required'],
			[['first_name', 'middle_name', 'last_name', 'caller_id'], 'string', 'max' => 255],
			[['caller_id'], 'unique', 'targetClass' => UserProfile::class],
		];
	}

	/**
	 * @return CreateUserProfileDto
	 * @throws Exception
	 */
	public function getDto(): CreateUserProfileDto
	{
		return new CreateUserProfileDto([
			'first_name'  => $this->first_name,
			'middle_name' => $this->middle_name,
			'last_name'   => $this->last_name,
			'caller_id'   => $this->caller_id,
			'phones'      => $this->phones,
			'emails'      => $this->emails
		]);
	}
}