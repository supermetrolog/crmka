<?php

namespace app\models\miniModels;

use app\kernel\common\models\AR\AR;
use app\models\Request;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "request_object_class".
 *
 * @property int     $id
 * @property int     $request_id
 * @property int     $object_class
 *
 * @property Request $request
 */
class RequestObjectClass extends AR
{
	public const MAIN_COLUMN = 'object_clas';

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'request_object_class';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['request_id', 'object_class'], 'required'],
			[['request_id', 'object_class'], 'integer'],
			[['request_id'], 'exist', 'skipOnError' => true, 'targetClass' => Request::class, 'targetAttribute' => ['request_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array
	{
		return [
			'id'           => 'ID',
			'request_id'   => 'Request ID',
			'object_class' => 'Object Class',
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
