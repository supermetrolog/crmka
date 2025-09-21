<?php

declare(strict_types=1);

namespace app\models\forms\Task;

use app\dto\Task\TaskAssignDto;
use app\kernel\common\models\Form\Form;
use app\models\User\User;
use Exception;

class TaskAssignForm extends Form
{
	public $user_id;
	public $assignedBy;
	public $comment;

	public function rules(): array
	{
		return [
			[['user_id', 'assignedBy', 'comment'], 'required'],
			[['user_id'], 'integer'],
			[['comment'], 'string', 'max' => 255],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']]
		];
	}

	public function attributeLabels(): array
	{
		return [
			'user_id' => 'ID пользователя',
			'comment' => 'Комментарий'
		];
	}

	/**
	 * @throws Exception
	 */
	public function getDto(): TaskAssignDto
	{
		return new TaskAssignDto([
			'user'       => User::find()->byId($this->user_id)->one(),
			'assignedBy' => $this->assignedBy,
			'comment'    => $this->comment
		]);

	}
}