<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\FieldQuery;

/**
 * This is the model class for table "field".
 *
 * @property int         $id
 * @property int         $field_type
 * @property int         $type
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class Field extends AR
{
	public const FIELD_TYPE_RADIO        = 1;
	public const FIELD_TYPE_CHECKBOX     = 2;
	public const FIELD_TYPE_TAB_CHECKBOX = 3;
	public const FIELD_TYPE_INPUT        = 4;
	public const FIELD_TYPE_TEXTAREA     = 5;

	public const TYPE_BOOLEAN = 1;
	public const TYPE_STRING  = 2;
	public const TYPE_INTEGER = 3;

	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;

	public static function tableName(): string
	{
		return 'field';
	}

	public function rules(): array
	{
		return [
			[['field_type', 'type'], 'required'],
			[['field_type', 'type'], 'integer'],
			['field_type', 'in', 'range' => self::getFieldType()],
			['type', 'in', 'range' => self::getType()],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'         => 'ID',
			'field_type' => 'Field Type',
			'type'       => 'Type',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'deleted_at' => 'Deleted At',
		];
	}

	public static function getFieldType(): array
	{
		return [
			self::FIELD_TYPE_RADIO,
			self::FIELD_TYPE_CHECKBOX,
			self::FIELD_TYPE_TAB_CHECKBOX,
			self::FIELD_TYPE_INPUT,
			self::FIELD_TYPE_TEXTAREA,
		];
	}

	public static function getType(): array
	{
		return [
			self::TYPE_BOOLEAN,
			self::TYPE_STRING,
			self::TYPE_INTEGER,
		];
	}

	public static function find(): FieldQuery
	{
		return new FieldQuery(get_called_class());
	}
}
