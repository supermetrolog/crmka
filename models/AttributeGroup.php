<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\AttributeGroupQuery;

/**
 * @property int     $id
 * @property string  $name
 * @property string  $created_at
 * @property string  $updated_at
 * @property ?string $deleted_at
 */
class AttributeGroup extends AR
{
	public static function tableName(): string
	{
		return 'attribute_group';
	}

	public function rules(): array
	{
		return [
			[['name'], 'required'],
			[['name'], 'string', 'max' => 64],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
		];
	}

	public static function find(): AttributeGroupQuery
	{
		return new AttributeGroupQuery(self::class);
	}
}