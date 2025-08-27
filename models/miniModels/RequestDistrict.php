<?php

namespace app\models\miniModels;

use app\kernel\common\models\AR\AR;
use app\models\Request;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "request_district".
 *
 * @property int     $id
 * @property int     $request_id
 * @property int     $district
 *
 * @property Request $request
 */
class RequestDistrict extends AR
{
	public const MAIN_COLUMN = 'district';

	private const DISTRICTS = [
		0  => 'ЦАО',
		1  => 'ЗАО',
		2  => 'СЗАО',
		3  => 'ЮЗАО',
		4  => 'ЮАО',
		5  => 'САО',
		6  => 'СВАО',
		7  => 'ЮВАО',
		8  => 'ВАО',
		9  => 'Нов. Москва',
		10 => 'Зеленоград',
	];

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'request_district';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['request_id', 'district'], 'required'],
			[['request_id', 'district'], 'integer'],
			[['request_id'], 'exist', 'skipOnError' => true, 'targetClass' => Request::class, 'targetAttribute' => ['request_id' => 'id']],
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
			'district'   => 'District',
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
		return self::DISTRICTS[$this->district];
	}
}
