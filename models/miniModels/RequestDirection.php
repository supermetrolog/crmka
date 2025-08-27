<?php

namespace app\models\miniModels;

use app\kernel\common\models\AR\AR;
use app\models\Request;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "request_direction".
 *
 * @property int     $id
 * @property int     $request_id
 * @property int     $direction
 *
 * @property Request $request
 */
class RequestDirection extends AR
{
	public const MAIN_COLUMN = 'direction';

	// @TODO переделать эту фигню
	private const DIRECTIONS = [
		0 => 'Север',
		1 => 'Восток',
		2 => 'Юг',
		3 => 'Запад',
		4 => 'Северо-Восток',
		5 => 'Юго-Восток',
		6 => 'Северо-Запад',
		7 => 'Юго-Запад',
	];

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'request_direction';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['request_id', 'direction'], 'required'],
			[['request_id', 'direction'], 'integer'],
			[['request_id'], 'exist', 'skipOnError' => true, 'targetClass' => Request::className(), 'targetAttribute' => ['request_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array
	{
		return [
			'id'         => 'ID',
			'request_id' => 'Request ID',
			'direction'  => 'Direction',
		];
	}

	/**
	 * Gets query for [[Request]].
	 *
	 * @return ActiveQuery
	 */
	public function getRequest(): ActiveQuery
	{
		return $this->hasOne(Request::class, ['id' => 'request_id']);
	}

	public function getName(): string
	{
		return self::DIRECTIONS[$this->direction];
	}
}
