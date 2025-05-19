<?php

namespace app\models\forms\TaskRelationEntity;

use app\dto\TaskRelationEntity\UpdateTaskRelationEntityDto;
use app\kernel\common\models\Form\Form;

class UpdateTaskRelationEntityForm extends Form
{
	public $comment;

	public function rules(): array
	{
		return [
			['comment', 'string', 'max' => 255]
		];
	}

	public function getDto(): UpdateTaskRelationEntityDto
	{
		return new UpdateTaskRelationEntityDto([
			'comment' => $this->comment
		]);
	}
}