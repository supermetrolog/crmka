<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\AttributeOptionQuery;
use app\models\ActiveQuery\AttributeQuery;

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
	public const DEFAULT_SORT_ORDER = 100;

	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;
	protected bool $useSoftDelete = true;

	public static function tableName(): string
	{
		return 'attribute_option';
	}

	public function rules(): array
	{
		return [
			[['attribute_id', 'value'], 'required'],
			[['attribute_id', 'sort_order'], 'integer'],
			['attribute_id', 'exist', 'targetClass' => Attribute::class, 'targetAttribute' => ['attribute_id' => 'id']],
			[['value', 'label'], 'string', 'max' => 128],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
		];
	}

	public static function find(): AttributeOptionQuery
	{
		return new AttributeOptionQuery(self::class);
	}

	public function getAttributeRel(): AttributeQuery
	{
		/** @var AttributeQuery */
		return $this->hasOne(Attribute::class, ['id' => 'attribute_id']);
	}
}