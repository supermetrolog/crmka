<?php

namespace app\models;

use app\enum\Attribute\AttributeInputTypeEnum;
use app\enum\Attribute\AttributeValueTypeEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\AttributeQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\User\User;

/**
 * @property int     $id
 * @property string  $kind
 * @property string  $label
 * @property ?string $description
 * @property string  $value_type
 * @property string  $input_type
 * @property int     $created_by_id
 * @property string  $created_at
 * @property string  $updated_at
 * @property ?string $deleted_at
 *
 * @property User    $created_by
 */
class Attribute extends AR
{
	protected bool $useSoftDelete = true;
	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;

	public static function tableName(): string
	{
		return 'attribute';
	}

	public function rules(): array
	{
		return [
			[['kind', 'label', 'value_type', 'input_type', 'created_by_id'], 'required'],
			[['kind', 'label'], 'string', 'max' => 64],
			[['value_type', 'input_type'], 'string', 'max' => 32],
			[['description'], 'string', 'max' => 255],
			[['value_type'], EnumValidator::class, 'enumClass' => AttributeValueTypeEnum::class],
			[['input_type'], EnumValidator::class, 'enumClass' => AttributeInputTypeEnum::class],
			[['created_by_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['created_by_id' => 'id']],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
		];
	}

	public function getUser(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'created_by_id']);
	}

	public static function find(): AttributeQuery
	{
		return new AttributeQuery(self::class);
	}
}