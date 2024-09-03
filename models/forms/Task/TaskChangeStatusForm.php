<?php

declare(strict_types=1);

namespace app\models\forms\Task;

use app\dto\Task\ChangeTaskStatusDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\Form\Form;
use app\models\Task;
use Exception;

/**
 *
 * @property-read ChangeTaskStatusDto $dto
 */
class TaskChangeStatusForm extends Form
{

	public $comment;
	public $status;
	public $impossible_to;
	public $changed_by_id;

	public function rules(): array
	{
		return [
			[['impossible_to'], 'safe'],
			[['status'], 'required'],
			[['comment'], 'string'],
			[['status', 'changed_by_id'], 'integer'],
			[['status'], 'in', 'range' => Task::getEditableStatuses()],
		];
	}

	/**
	 * @return ChangeTaskStatusDto
	 * @throws Exception
	 */
	public function getDto(): ChangeTaskStatusDto
	{
		return new ChangeTaskStatusDto([
			'status'        => $this->status,
			'impossible_to' => DateTimeHelper::tryMake($this->impossible_to),
			'comment'       => $this->comment,
			'changed_by_id' => $this->changed_by_id,
		]);

	}
}