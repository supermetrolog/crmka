<?php

namespace app\models\forms\Utilities;

use app\dto\Utilities\FixObjectPurposesUtilitiesDto;
use app\kernel\common\models\Form\Form;
use app\models\Objects;

class UtilitiesFixPurposesForm extends Form
{
	public $object_id;
	public $purposes = [];

	public function rules(): array
	{
		return [
			[['object_id', 'purposes'], 'required'],
			['object_id', 'integer'],
			['object_id', 'exist', 'targetClass' => Objects::class, 'targetAttribute' => ['object_id' => 'id']],
			['purposes', 'safe']
		];
	}

	public function attributeLabels(): array
	{
		return [
			'object_id' => 'ID объекта',
			'purposes'  => 'Назначения объекта'
		];
	}

	public function getDto(): FixObjectPurposesUtilitiesDto
	{
		return new FixObjectPurposesUtilitiesDto([
			'object'   => Objects::find()->byId((int)$this->object_id),
			'purposes' => $this->purposes
		]);
	}
}