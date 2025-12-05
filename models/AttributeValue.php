<?php

namespace app\models;

use app\helpers\validators\MorphExistValidator;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\AttributeQuery;
use app\models\ActiveQuery\AttributeValueQuery;

/**
 * @property int            $id
 * @property int            $attribute_id
 * @property int            $entity_id
 * @property string         $entity_type
 * @property string         $value
 * @property string         $created_at
 * @property string         $updated_at
 * @property ?string        $deleted_at
 *
 * @property-read Attribute $attributeRel
 */
class AttributeValue extends AR
{
	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;
	protected bool $useSoftCreate = true;

	public static function tableName(): string
	{
		return 'attribute_value';
	}

	public function rules(): array
	{
		return [
			[['attribute_id', 'entity_id', 'entity_type'], 'required'],
			[['attribute_id', 'entity_id'], 'integer'],
			['attribute_id', 'exist', 'targetClass' => Attribute::class, 'targetAttribute' => ['attribute_id' => 'id']],
			['entity_id', MorphExistValidator::class, 'targetClassMap' => AttributeRelationEntity::getEntityMorphMap()],
			[['entity_type'], 'string', 'max' => 64],
			[['value'], 'string'],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
		];
	}

	public static function find(): AttributeValueQuery
	{
		return new AttributeValueQuery(self::class);
	}

	public function getAttributeRel(): AttributeQuery
	{
		/** @var AttributeQuery */
		return $this->hasOne(Attribute::class, ['id' => 'attribute_id']);
	}
}