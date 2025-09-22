<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\AlertQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\User\User;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "alert".
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $message
 * @property string      $created_by_type
 * @property int         $created_by_id
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 *
 * @property User        $user
 * @property User        $createdByUser
 * @property User        $createdBy
 */
class Alert extends AR
{

	public static function tableName(): string
	{
		return 'alert';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'message', 'created_by_type', 'created_by_id'], 'required'],
			[['user_id', 'created_by_id'], 'integer'],
			[['message'], 'string'],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
			[['created_by_type'], 'string', 'max' => 255],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'              => 'ID',
			'user_id'         => 'User ID',
			'message'         => 'Message',
			'created_by_type' => 'Created By Type',
			'created_by_id'   => 'Created By ID',
			'created_at'      => 'Created At',
			'updated_at'      => 'Updated At',
			'deleted_at'      => 'Deleted At',
		];
	}

	/**
	 * @return ActiveQuery|UserQuery
	 */
	public function getUser(): UserQuery
	{
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getCreatedByUser(): ActiveQuery
	{
		return $this->morphBelongTo(User::class, 'id', 'created_by');
	}

	public function getCreatedBy(): AR
	{
		return $this->createdByUser;
	}

	public static function find(): AlertQuery
	{
		return new AlertQuery(get_called_class());
	}
}
