<?php

declare(strict_types=1);

namespace app\models\forms\Task;

use app\dto\Task\LinkTaskRelationEntityDto;
use app\helpers\validators\MorphExistValidator;
use app\kernel\common\models\Form\Form;
use app\models\TaskRelationEntity;
use Exception;

class TaskRelationEntityLinkForm extends Form
{
	public $entity_id;
	public $entity_type;
	public $comment;

	public function rules(): array
	{
		return [
			[['entity_id', 'entity_type'], 'required'],
			[['comment'], 'string', 'max' => 255],
			[['entity_id'], 'integer'],
			['entity_id', MorphExistValidator::class, 'targetClassMap' => TaskRelationEntity::getEntityMorphMap()],
			['entity_type', 'string'],
			['entity_type', 'in', 'range' => TaskRelationEntity::getAvailableEntityTypes()]
		];
	}

	public function attributeLabels(): array
	{
		return [
			'entity_id'   => 'ID сущности',
			'entity_type' => 'Тип сущности',
			'comment'     => 'Комментарий'
		];
	}

	/**
	 * @throws Exception
	 */
	public function getDto(): LinkTaskRelationEntityDto
	{
		return new LinkTaskRelationEntityDto([
			'entityId'     => $this->entity_id,
			'entityType'   => $this->entity_type,
			'comment'      => $this->comment,
			'relationType' => TaskRelationEntity::RELATION_TYPE_MANUAL
		]);

	}
}