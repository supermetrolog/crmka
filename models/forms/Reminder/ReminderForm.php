<?php

declare(strict_types=1);

namespace app\models\forms\Reminder;

use app\dto\Reminder\CreateReminderDto;
use app\dto\Reminder\CreateReminderForUsersDto;
use app\dto\Reminder\UpdateReminderDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\Form\Form;
use app\models\Reminder;
use app\models\User\User;
use Exception;

class ReminderForm extends Form
{

	public const SCENARIO_CREATE_FOR_USERS = 'scenario_create_for_users';
	public const SCENARIO_CREATE           = 'scenario_create';
	public const SCENARIO_UPDATE           = 'scenario_update';

	public $user_ids;
	public $user_id;
	public $created_by_type;
	public $created_by_id;
	public $message;
	public $status;
	public $notify_at;

	public function rules(): array
	{
		return [
			[['user_id', 'message', 'status', 'created_by_type', 'created_by_id', 'user_ids'], 'required'],
			[['user_id', 'status', 'created_by_id'], 'integer'],
			[['message'], 'string'],
			[['notify_at'], 'safe'],
			[['created_by_type'], 'string', 'max' => 255],
			['status', 'in', 'range' => Reminder::getStatuses()],
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
			'status',
			'notify_at'
		];

		return [
			self::SCENARIO_CREATE           => [...$common, 'created_by_id', 'created_by_type', 'user_id'],
			self::SCENARIO_CREATE_FOR_USERS => [...$common, 'created_by_id', 'created_by_type', 'user_ids'],
			self::SCENARIO_UPDATE           => [...$common, 'user_id'],
		];
	}

	/**
	 * @return CreateReminderDto|UpdateReminderDto|CreateReminderForUsersDto
	 * @throws Exception
	 */
	public function getDto()
	{
		if ($this->getScenario() === self::SCENARIO_CREATE) {
			return new CreateReminderDto([
				'user'            => User::find()->byId($this->user_id)->one(),
				'message'         => $this->message,
				'status'          => Reminder::STATUS_CREATED,
				'created_by_type' => $this->created_by_type,
				'created_by_id'   => $this->created_by_id,
				'notify_at'       => DateTimeHelper::tryMake($this->notify_at),
			]);
		}

		if ($this->getScenario() === self::SCENARIO_CREATE_FOR_USERS) {
			return new CreateReminderForUsersDto([
				'users'           => User::find()->byIds($this->user_ids)->all(),
				'message'         => $this->message,
				'status'          => Reminder::STATUS_CREATED,
				'created_by_type' => $this->created_by_type,
				'created_by_id'   => $this->created_by_id,
				'notify_at'       => DateTimeHelper::tryMake($this->notify_at),
			]);
		}

		return new UpdateReminderDto([
			'user'      => User::find()->byId($this->user_id)->one(),
			'message'   => $this->message,
			'status'    => $this->status,
			'notify_at' => DateTimeHelper::tryMake($this->notify_at),
		]);
	}
}