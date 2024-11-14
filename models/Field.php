<?php

namespace app\models;

use app\helpers\ArrayHelper;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\FieldQuery;

/**
 * This is the model class for table "field".
 *
 * @property int         $id
 * @property string      $field_type
 * @property string      $type
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class Field extends AR
{
	public const FIELD_TYPE_RADIO        = 'radio';
	public const FIELD_TYPE_CHECKBOX     = 'checkbox';
	public const FIELD_TYPE_TAB_CHECKBOX = 'tab-checkbox';
	public const FIELD_TYPE_INPUT        = 'input';
	public const FIELD_TYPE_TEXTAREA     = 'textarea';
	public const FIELD_TYPE_CUSTOM       = 'custom';

	public const TYPE_BOOLEAN = 'boolean';
	public const TYPE_STRING  = 'string';
	public const TYPE_INTEGER = 'integer';
	public const TYPE_JSON    = 'json';

	protected const convertableToBool = [
		self::TYPE_BOOLEAN,
		self::TYPE_INTEGER
	];

	protected const convertableToString = [
		self::TYPE_STRING,
		self::TYPE_INTEGER
	];

	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;
	protected bool $useSoftCreate = true;

	public static function tableName(): string
	{
		return 'field';
	}

	public function rules(): array
	{
		return [
			[['field_type', 'type'], 'required'],
			[['field_type', 'type'], 'string', 'max' => 255],
			['field_type', 'in', 'range' => self::getFieldTypes()],
			['type', 'in', 'range' => self::getTypes()],
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

	public static function getFieldTypes(): array
	{
		return [
			self::FIELD_TYPE_RADIO,
			self::FIELD_TYPE_CHECKBOX,
			self::FIELD_TYPE_TAB_CHECKBOX,
			self::FIELD_TYPE_INPUT,
			self::FIELD_TYPE_TEXTAREA,
			self::FIELD_TYPE_CUSTOM
		];
	}

	public static function getTypes(): array
	{
		return [
			self::TYPE_BOOLEAN,
			self::TYPE_STRING,
			self::TYPE_INTEGER,
			self::TYPE_JSON
		];
	}

	public function canBeConvertedToBool(): bool
	{
		return ArrayHelper::includes(self::convertableToBool, $this->type);
	}

	public function canBeConvertedToJSON(): bool
	{
		return $this->type === self::TYPE_JSON;
	}

	public function canBeConvertedToString(): bool
	{
		return ArrayHelper::includes(self::convertableToString, $this->type);
	}

	public function canBeConvertedToInteger(): bool
	{
		return $this->type === self::TYPE_INTEGER;
	}

	public static function find(): FieldQuery
	{
		return new FieldQuery(get_called_class());
	}
}
