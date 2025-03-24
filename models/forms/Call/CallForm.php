<?php

declare(strict_types=1);

namespace app\models\forms\Call;

use app\dto\Call\CreateCallDto;
use app\dto\Call\UpdateCallDto;
use app\kernel\common\models\Form\Form;
use app\models\Call;
use app\models\Contact;
use app\models\User;
use Exception;

class CallForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $user_id;
	public $contact_id;
	public $status;
	public $type;
	public $description;

	public function rules(): array
	{
		return [
			[['user_id', 'contact_id', 'status', 'type'], 'required'],
			[['user_id', 'contact_id', 'status', 'type'], 'integer'],
			['description', 'string', 'max' => 512],
			['status', 'in', 'range' => Call::getStatuses()],
			['type', 'in', 'range' => Call::getTypes()],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			[['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::class, 'targetAttribute' => ['contact_id' => 'id']],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'contact_id',
			'status',
			'type',
			'description'
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'user_id'],
			self::SCENARIO_UPDATE => $common,
		];
	}

	public function attributeLabels(): array
	{
		return [
			'user_id'     => 'ID сотрудника',
			'contact_id'  => 'ID контакта',
			'type'        => 'Тип звонка',
			'status'      => 'Статус звонка',
			'description' => 'Описание',
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
					'user'        => User::find()->byId((int)$this->user_id)->one(),
					'contact'     => Contact::find()->byId((int)$this->contact_id)->one(),
					'type'        => $this->type,
					'status'      => $this->status,
					'description' => $this->description
				]);

			default:
				return new UpdateCallDto([
					'contact'     => Contact::find()->byId((int)$this->contact_id)->one(),
					'type'        => $this->type,
					'status'      => $this->status,
					'description' => $this->description
				]);
		}
	}
}