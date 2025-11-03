<?php

namespace app\models;

use app\enum\Attribute\AttributeInputTypeEnum;
use app\enum\Attribute\AttributeValueTypeEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;

/**
 * @property int     $id
 * @property string  $kind
 * @property string  $label
 * @property ?string $description
 * @property string  $value_type
 * @property string  $input_type
 * @property string  $created_at
 * @property string  $updated_at
 * @property ?string $deleted_at
 */
class Attribute extends AR
{
	public static function tableName(): string
	{
		return 'attribute';
	}

	public function rules(): array
	{
		return [
			[['kind', 'label', 'value_type', 'input_type'], 'required'],
			[['kind', 'label'], 'string', 'max' => 64],
			[['value_type', 'input_type'], 'string', 'max' => 32],
			[['description'], 'string', 'max' => 255],
			[['value_type'], EnumValidator::class, 'enumClass' => AttributeValueTypeEnum::class],
			[['input_type'], EnumValidator::class, 'enumClass' => AttributeInputTypeEnum::class],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
		];
	}

	public static function find(): AQ
	{
		return new AQ(static::class);
	}
}