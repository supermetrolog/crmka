<?php

namespace app\models;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;

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
	public static function tableName(): string
	{
		return 'attribute_value';
	}

	public function rules(): array
	{
		return [
			[['attribute_id', 'entity_id', 'entity_type'], 'required'],
			[['attribute_id', 'entity_id'], 'integer'],
			[['entity_type'], 'string', 'max' => 64],
			[['value'], 'string'],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
		];
	}

	public static function find(): AQ
	{
		return new AQ(static::class);
	}

	public function getAttributeRel(): AQ
	{
		/** @var AQ */
		return $this->hasOne(Attribute::class, ['id' => 'attribute_id']);
	}
}