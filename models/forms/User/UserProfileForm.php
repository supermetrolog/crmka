<?php

declare(strict_types=1);

namespace app\models\forms\User;

use app\dto\User\CreateUserProfileDto;
use app\kernel\common\models\Form\Form;
use app\models\UserProfile;
use Exception;

class UserProfileForm extends Form
{
	public ?int $id = null;

	public $first_name;
	public $middle_name;
	public $last_name;
	public $caller_id;

	public function rules(): array
	{
		return [
			[['first_name', 'middle_name'], 'required'],
			[['first_name', 'middle_name', 'last_name', 'caller_id'], 'string', 'max' => 255],
			[['id'], 'integer'],
			[['id'], 'exist', 'skipOnEmpty' => true, 'targetClass' => UserProfile::class, 'targetAttribute' => ['id' => 'id']],
			[['caller_id'], 'unique', 'targetClass' => UserProfile::class, 'filter' => function ($query) {
				if (!is_null($this->id)) {
					$query->andWhere(['!=', 'id', $this->id]);
				}
			}],
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
			'caller_id'   => $this->caller_id
		]);
	}
}