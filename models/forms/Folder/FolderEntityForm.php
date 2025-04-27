<?php

declare(strict_types=1);

namespace app\models\forms\Folder;

use app\dto\Folder\EntityInFolderDto;
use app\kernel\common\models\AR\AR;
use app\kernel\common\models\Form\Form;
use app\models\FolderEntity;

class FolderEntityForm extends Form
{
	public $entity_id;
	public $entity_type;

	public function rules(): array
	{
		return [
			[['entity_id', 'entity_type'], 'required'],
			['entity_id', 'integer'],
			['entity_id', 'validateEntity'],
			['entity_type', 'string'],
			['entity_type', 'in', 'range' => FolderEntity::getAvailableTypes()]
		];
	}

	public function validateEntity($attribute, $params): void
	{
		$map = FolderEntity::getMorphMap();

		if (!isset($map[$this->entity_type])) {
			$this->addError('entity_type', 'Неверный тип сущности');

			return;
		}

		/** @var $class class-string<AR> */
		$class = $map[$this->entity_type];

		if (!$class::find()->andWhere(['id' => $this->entity_id])->exists()) {
			$this->addError($attribute, 'Сущность не найдена');
		}
	}

	public function attributeLabels(): array
	{
		return [
			'entity_id'   => 'ID сущности',
			'entity_type' => 'Тип сущности'
		];
	}

	public function getDto(): EntityInFolderDto
	{
		return new EntityInFolderDto([
			'entity_id'   => $this->entity_id,
			'entity_type' => $this->entity_type
		]);
	}
}