<?php

namespace app\models;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;

/**
 * @property int            $id
 * @property int            $attribute_id
 * @property int            $sort_order
 * @property ?string        $label
 * @property string         $value
 * @property string         $created_at
 * @property string         $updated_at
 * @property ?string        $deleted_at
 *
 * @property-read Attribute $attributeRel
 */
class AttributeOption extends AR
{
	public static function tableName(): string
	{
		return 'attribute_option';
	}

	public function rules(): array
	{
		return [
			[['attribute_id', 'value'], 'required'],
			[['attribute_id', 'sort_order'], 'integer'],
			[['value', 'label'], 'string', 'max' => 128],
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