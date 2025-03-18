<?php

declare(strict_types=1);

namespace app\models\forms\Task;

use app\dto\Task\CreateTaskDto;
use app\dto\Task\CreateTaskForUsersDto;
use app\dto\Task\UpdateTaskDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\Form\Form;
use app\models\Media;
use app\models\Task;
use app\models\TaskTag;
use app\models\User;
use Exception;

class TaskForm extends Form
{

	public const SCENARIO_CREATE_FOR_USERS = 'scenario_create_for_users';
	public const SCENARIO_CREATE           = 'scenario_create';
	public const SCENARIO_UPDATE           = 'scenario_update';

	public $user_ids      = [];
	public $user_id;
	public $created_by_type;
	public $created_by_id;
	public $message;
	public $title;
	public $status;
	public $start;
	public $end;
	public $tag_ids       = [];
	public $observer_ids  = [];
	public $survey_id;
	public $current_files = [];

	public function rules(): array
	{
		return [
			[['user_id', 'title', 'status', 'created_by_type', 'created_by_id', 'user_ids'], 'required'],
			[['user_id', 'status', 'created_by_id', 'survey_id'], 'integer'],
			[['message'], 'string'],
			[['title'], 'string', 'max' => 255, 'min' => 16],
			[['start', 'end'], 'safe'],
			[['created_by_type'], 'string', 'max' => 255],
			['status', 'in', 'range' => Task::getStatuses()],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			['user_ids', 'each', 'rule' => [
				'exist',
				'targetClass'     => User::class,
				'targetAttribute' => ['user_ids' => 'id'],
			]],
			['tag_ids', 'each', 'rule' => [
				'exist',
				'targetClass'     => TaskTag::class,
				'targetAttribute' => ['tag_ids' => 'id'],
			]],
			['current_files', 'each', 'rule' => [
				'exist',
				'targetClass'     => Media::class,
				'targetAttribute' => ['current_files' => 'id']
			]],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'user_id'       => 'ID пользователя',
			'user_ids'      => 'ID пользователей',
			'message'       => 'Описание',
			'title'         => 'Заголовок',
			'status'        => 'Статус',
			'start'         => 'Дата старта',
			'end'           => 'Дата окончания',
			'tag_ids'       => 'Тэги',
			'observer_ids'  => 'Наблюдители',
			'survey_id'     => 'ID опроса',
			'current_files' => 'Текущие файлы'
		];
	}

	public function scenarios(): array
	{
		$common = [
			'message',
			'title',
			'status',
			'start',
			'end',
			'tag_ids',
			'observer_ids'
		];

		return [
			self::SCENARIO_CREATE           => [...$common, 'created_by_id', 'created_by_type', 'user_id', 'survey_id'],
			self::SCENARIO_CREATE_FOR_USERS => [...$common, 'created_by_id', 'created_by_type', 'user_ids', 'survey_id'],
			self::SCENARIO_UPDATE           => [...$common, 'user_id', 'current_files'],
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
				'user'            => User::find()->byId((int)$this->user_id)->one(),
				'message'         => $this->message,
				'title'           => $this->title,
				'status'          => Task::STATUS_CREATED,
				'start'           => DateTimeHelper::tryMake($this->start),
				'end'             => DateTimeHelper::tryMake($this->end),
				'created_by_type' => $this->created_by_type,
				'created_by_id'   => $this->created_by_id,
				'tagIds'          => $this->tag_ids,
				'observerIds'     => $this->observer_ids,
				'surveyId'        => $this->survey_id
			]);
		}

		if ($this->getScenario() === self::SCENARIO_CREATE_FOR_USERS) {
			return new CreateTaskForUsersDto([
				'users'           => User::find()->byIds($this->user_ids)->all(),
				'message'         => $this->message,
				'title'           => $this->title,
				'status'          => Task::STATUS_CREATED,
				'start'           => DateTimeHelper::tryMake($this->start),
				'end'             => DateTimeHelper::tryMake($this->end),
				'created_by_type' => $this->created_by_type,
				'created_by_id'   => $this->created_by_id,
				'tagIds'          => $this->tag_ids,
				'observerIds'     => $this->observer_ids,
				'surveyId'        => $this->survey_id
			]);
		}

		return new UpdateTaskDto([
			'user'          => User::find()->byId((int)$this->user_id)->one(),
			'message'       => $this->message,
			'title'         => $this->title,
			'status'        => $this->status,
			'start'         => $this->start,
			'end'           => $this->end,
			'tagIds'        => $this->tag_ids,
			'observerIds'   => $this->observer_ids,
			'created_by_id' => $this->created_by_id,
			'currentFiles'  => $this->current_files
		]);
	}
}