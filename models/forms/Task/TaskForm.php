<?php

declare(strict_types=1);

namespace app\models\forms\Task;

use app\dto\Task\CreateTaskDto;
use app\dto\Task\CreateTaskForUsersDto;
use app\dto\Task\UpdateTaskDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\Form;
use app\models\Task;
use app\models\User;
use Exception;

class TaskForm extends Form
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
	public $start;
	public $end;

	public function rules(): array
	{
		return [
			[['user_id', 'message', 'status', 'created_by_type', 'created_by_id', 'user_ids'], 'required'],
			[['user_id', 'status', 'created_by_id'], 'integer'],
			[['message'], 'string'],
			[['start', 'end'], 'safe'],
			[['created_by_type'], 'string', 'max' => 255],
			['status', 'in', 'range' => Task::getStatuses()],
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
			'start',
			'end'
		];

		return [
			self::SCENARIO_CREATE           => [...$common, 'created_by_id', 'created_by_type', 'user_id'],
			self::SCENARIO_CREATE_FOR_USERS => [...$common, 'created_by_id', 'created_by_type', 'user_ids'],
			self::SCENARIO_UPDATE           => [...$common, 'user_id'],
		];
	}

	/**
	 * @return CreateTaskDto|UpdateTaskDto|CreateTaskForUsersDto
	 * @throws Exception
	 */
	public function getDto()
	{
		if ($this->getScenario() === self::SCENARIO_CREATE) {
			return new CreateTaskDto([
				'user'            => User::find()->byId($this->user_id)->one(),
				'message'         => $this->message,
				'status'          => Task::STATUS_CREATED,
				'start'           => DateTimeHelper::tryMake($this->start),
				'end'             => DateTimeHelper::tryMake($this->end),
				'created_by_type' => $this->created_by_type,
				'created_by_id'   => $this->created_by_id,
			]);
		}

		if ($this->getScenario() === self::SCENARIO_CREATE_FOR_USERS) {
			return new CreateTaskForUsersDto([
				'users'           => User::find()->byIds($this->user_ids)->all(),
				'message'         => $this->message,
				'status'          => Task::STATUS_CREATED,
				'start'           => DateTimeHelper::tryMake($this->start),
				'end'             => DateTimeHelper::tryMake($this->end),
				'created_by_type' => $this->created_by_type,
				'created_by_id'   => $this->created_by_id,
			]);
		}

		return new UpdateTaskDto([
			'user'    => User::find()->byId($this->user_id)->one(),
			'message' => $this->message,
			'status'  => $this->status,
			'start'   => DateTimeHelper::tryMake($this->start),
			'end'     => DateTimeHelper::tryMake($this->end),
		]);
	}
}