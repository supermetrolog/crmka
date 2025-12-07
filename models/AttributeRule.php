<?php

namespace app\models;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\AttributeGroupQuery;
use app\models\ActiveQuery\AttributeQuery;
use app\models\ActiveQuery\AttributeRuleQuery;

/**
 * @property int                  $id
 * @property int                  $attribute_id
 * @property ?int                 $attribute_group_id
 * @property string               $entity_type
 * @property boolean              $is_required
 * @property boolean              $is_inheritable
 * @property boolean              $is_editable
 * @property string               $status
 * @property int                  $sort_order
 * @property string               $created_at
 * @property string               $updated_at
 * @property ?string              $deleted_at
 *
 * @property-read Attribute       $attributeRel
 * @property-read ?AttributeGroup $attributeGroup
 */
class AttributeRule extends AR
{
	public const DEFAULT_SORT_ORDER = 10;

	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;
	protected bool $useSoftDelete = true;

	public static function tableName(): string
	{
		return 'attribute_rule';
	}

	public function rules(): array
	{
		return [
			[['attribute_id', 'entity_type', 'is_required', 'is_inheritable', 'is_editable', 'status'], 'required'],
			[['attribute_id', 'attribute_group_id', 'sort_order'], 'integer'],
			[['is_required', 'is_inheritable', 'is_editable'], 'boolean'],
			[['entity_type'], 'string', 'max' => 64],
			[['status'], 'string', 'max' => 32],
			['attribute_id', 'exist', 'targetClass' => Attribute::class, 'targetAttribute' => ['attribute_id' => 'id']],
			['attribute_group_id', 'exist', 'targetClass' => AttributeGroup::class, 'targetAttribute' => ['attribute_group_id' => 'id']],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
		];
	}

	public static function find(): AttributeRuleQuery
	{
		/** @var AttributeRuleQuery */
		return new AQ(self::class);
	}

	public function getAttributeRel(): AttributeQuery
	{
		/** @var AttributeQuery */
		return $this->hasOne(Attribute::class, ['id' => 'attribute_id']);
	}

	public function getAttributeGroup(): AttributeGroupQuery
	{
		/** @var AttributeGroupQuery */
		return $this->hasOne(AttributeGroup::class, ['id' => 'attribute_group_id']);
	}
}