<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\EffectQuery;

/**
 * This is the model class for table "question_answer".
 *
 * @property int     $id
 * @property string  $title
 * @property string  $kind
 * @property bool    $active
 * @property ?string $description
 *
 */
class Effect extends AR
{
	public static function tableName(): string
	{
		return 'effect';
	}

	public function rules(): array
	{
		return [
			[['kind', 'title'], 'required'],
			[['kind', 'title'], 'string', 'max' => 64],
			[['kind'], 'unique'],
			[['active'], 'boolean'],
			[['description'], 'string', 'max' => 255],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'          => 'ID',
			'title'       => 'Title',
			'kind'        => 'Kind',
			'active'      => 'Active',
			'description' => 'Description',
		];
	}

	public function isActive(): bool
	{
		return $this->active;
	}

	public static function find(): EffectQuery
	{
		return new EffectQuery(static::class);
	}
}
