<?php

namespace app\models\miniModels;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\RequestQuery;
use app\models\oldDb\location\Region;
use app\models\Request;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "request_region".
 *
 * @property int         $id
 * @property int         $request_id
 * @property int         $region
 *
 * @property Request     $request
 * @property-read Region $info
 */
class RequestRegion extends AR
{
	public const MAIN_COLUMN = 'region';

	public static function tableName(): string
	{
		return 'request_region';
	}
	
	public function rules(): array
	{
		return [
			[['request_id', 'region'], 'required'],
			[['request_id', 'region'], 'integer'],
			[['request_id'], 'exist', 'skipOnError' => true, 'targetClass' => Request::class, 'targetAttribute' => ['request_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'         => 'ID',
			'request_id' => 'Request ID',
			'region'     => 'Region',
		];
	}

	public function getRequest(): RequestQuery
	{
		/** @var RequestQuery */
		return $this->hasOne(Request::class, ['id' => 'request_id']);
	}

	public function getInfo(): ActiveQuery
	{
		return $this->hasOne(Region::class, ['id' => 'region']);
	}
}
