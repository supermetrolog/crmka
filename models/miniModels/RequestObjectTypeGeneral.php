<?php

namespace app\models\miniModels;

use app\kernel\common\models\AR\AR;
use app\models\Request;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "request_object_type_general".
 *
 * @property int     $id
 * @property int     $request_id [СВЯЗЬ] с запросом
 * @property int     $type       Тип объекта (0 - склад, 1 - производство, 2 - участок
 *
 * @property Request $request
 */
class RequestObjectTypeGeneral extends AR
{

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'request_object_type_general';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['request_id', 'type'], 'required'],
			[['request_id', 'type'], 'integer'],
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
			'type'       => 'Type',
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
