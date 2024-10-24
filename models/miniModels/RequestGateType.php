<?php

namespace app\models\miniModels;

use app\kernel\common\models\AR\AR;
use app\models\Request;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "request_gate_type".
 *
 * @property int     $id
 * @property int     $request_id
 * @property int     $gate_type
 *
 * @property Request $request
 */
class RequestGateType extends AR
{
	public const MAIN_COLUMN = 'gate_type';

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'request_gate_type';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['request_id', 'gate_type'], 'required'],
			[['request_id', 'gate_type'], 'integer'],
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
			'gate_type'  => 'Gate Type',
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
}
