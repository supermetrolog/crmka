<?php

namespace app\models\miniModels;

use app\kernel\common\models\AR\AR;
use app\models\oldDb\location\Region;
use app\models\Request;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "request_region".
 *
 * @property int     $id
 * @property int     $request_id
 * @property int     $region
 *
 * @property Request $request
 */
class RequestRegion extends AR
{
	public const MAIN_COLUMN = 'region';

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'request_region';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['request_id', 'region'], 'required'],
			[['request_id', 'region'], 'integer'],
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
			'region'     => 'Region',
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

	/**
	 * Gets query for [[RequestRegions]].
	 *
	 * @return ActiveQuery
	 */
	public function getInfo(): ActiveQuery
	{
		return $this->hasOne(Region::class, ['id' => 'region']);
	}
}
