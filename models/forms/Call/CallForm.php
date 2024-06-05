<?php

declare(strict_types=1);

namespace app\models\forms\Call;

use app\dto\Call\CreateCallDto;
use app\dto\Call\UpdateCallDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\Form\Form;
use app\models\Contact;
use app\models\Reminder;
use app\models\User;
use Exception;

class CallForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $user_id;
	public $contact_id;

	public function rules(): array
	{
		return [
			[['user_id'], 'required'],
			[['user_id', 'contact_id'], 'integer'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'user_id',
			'contact_id',
		];

		return [
			self::SCENARIO_CREATE => [...$common],
			self::SCENARIO_UPDATE => [...$common],
		];
	}

	/**
	 * @return CreateCallDto|UpdateCallDto
	 * @throws Exception
	 */
	public function getDto()
	{
		switch ($this->getScenario()) {
			case self::SCENARIO_CREATE:
				return new CreateCallDto([
					'user'    => User::find()->byId($this->user_id)->one(),
					'contact' => Contact::find()->where(['id' => $this->contact_id])->one(),
				]);

			default:
				return new UpdateCallDto([
					'user'    => User::find()->byId($this->user_id)->one(),
					'contact' => Contact::find()->where(['id' => $this->contact_id])->one(),
				]);
		}
	}
}