<?php

declare(strict_types=1);

namespace app\models\forms\Alert;

use app\dto\Alert\CreateAlertDto;
use app\dto\Alert\CreateAlertForUsersDto;
use app\dto\Alert\UpdateAlertDto;
use app\kernel\common\models\Form\Form;
use app\models\User;
use Exception;

class AlertForm extends Form
{

	public const SCENARIO_CREATE_FOR_USERS = 'scenario_create_for_users';
	public const SCENARIO_CREATE           = 'scenario_create';
	public const SCENARIO_UPDATE           = 'scenario_update';

	public $user_ids;
	public $user_id;
	public $created_by_type;
	public $created_by_id;
	public $message;

	public function rules(): array
	{
		return [
			[['user_id', 'message', 'created_by_type', 'created_by_id', 'user_ids'], 'required'],
			[['user_id', 'created_by_id'], 'integer'],
			[['message'], 'string'],
			[['created_by_type'], 'string', 'max' => 255],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			['user_ids', 'each', 'rule' => [
				'exist',
				'targetClass'     => User::class,
				'targetAttribute' => ['user_ids' => 'id'],
			],]
		];
	}

	public function scenarios(): array
	{
		$common = [
			'message',
		];

		return [
			self::SCENARIO_CREATE           => [...$common, 'created_by_id', 'created_by_type', 'user_id'],
			self::SCENARIO_CREATE_FOR_USERS => [...$common, 'created_by_id', 'created_by_type', 'user_ids'],
			self::SCENARIO_UPDATE           => [...$common, 'user_id'],
		];
	}

	/**
	 * @return CreateAlertDto|UpdateAlertDto|CreateAlertForUsersDto
	 * @throws Exception
	 */
	public function getDto()
	{
		if ($this->getScenario() === self::SCENARIO_CREATE) {
			return new CreateAlertDto([
				'user'            => User::find()->byId($this->user_id)->one(),
				'message'         => $this->message,
				'created_by_type' => $this->created_by_type,
				'created_by_id'   => $this->created_by_id,
			]);
		}

		if ($this->getScenario() === self::SCENARIO_CREATE_FOR_USERS) {
			return new CreateAlertForUsersDto([
				'users'           => User::find()->byIds($this->user_ids)->all(),
				'message'         => $this->message,
				'created_by_type' => $this->created_by_type,
				'created_by_id'   => $this->created_by_id,
			]);
		}

		return new UpdateAlertDto([
			'user'    => User::find()->byId($this->user_id)->one(),
			'message' => $this->message,
		]);
	}
}