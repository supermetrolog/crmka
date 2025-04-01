<?php

declare(strict_types=1);

namespace app\models\forms\Request;

use app\dto\Request\CloneRequestDto;
use app\kernel\common\models\Form\Form;
use app\models\User;

class RequestCloneForm extends Form
{
	public $consultant_id;

	public function rules(): array
	{
		return [
			[['consultant_id'], 'required'],
			[['consultant_id'], 'integer'],
			[['consultant_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['consultant_id' => 'id']]
		];
	}

	public function attributeLabels(): array
	{
		return [
			'consultant_id' => 'ID консультанта',
		];
	}

	public function getDto(): CloneRequestDto
	{
		return new CloneRequestDto([
			'consultant' => User::findOne((int)$this->consultant_id)
		]);
	}
}