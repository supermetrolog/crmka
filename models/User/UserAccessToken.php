<?php

namespace app\models\User;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\UserAccessTokenQuery;
use app\models\ActiveQuery\UserQuery;

/**
 * This is the model class for table "user_access_token".
 *
 * @property int       $id
 * @property int       $user_id
 * @property string    $access_token
 * @property string    $expires_at
 * @property string    $created_at
 * @property string    $updated_at
 * @property string    $deleted_at
 * @property string    $user_agent
 * @property string    $ip
 *
 * @property-read User $user
 */
class UserAccessToken extends AR
{
	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;

	const EXPIRES_IN_DAYS = 30;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'user_access_token';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['user_id', 'access_token'], 'required'],
			[['user_id'], 'integer'],
			[['access_token'], 'string', 'max' => 255],
			[['user_agent'], 'string', 'max' => 1024],
			[['ip'], 'string', 'max' => 15],
			[['created_at', 'updated_at', 'expires_at'], 'safe'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],

		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array
	{
		return [
			'id'           => 'ID',
			'user_id'      => 'User ID',
			'access_token' => 'Access Token',
			'expires_at'   => 'Expires At',
			'created_at'   => 'Created At',
			'updated_at'   => 'Updated At',
		];
	}

	/**
	 * @return bool
	 */
	public function isValid(): bool
	{
		return $this->expires_at > date('Y-m-d H:i:s');
	}

	/**
	 * Returns the user query used by this AR class.
	 *
	 * @return UserQuery
	 */
	public function getUser(): UserQuery
	{
		/** @var UserQuery $query */
		$query = $this->hasOne(User::class, ['id' => 'user_id']);

		return $query;
	}

	/**
	 * {@inheritdoc}
	 * @return UserAccessTokenQuery the active query used by this AR class.
	 */
	public static function find(): UserAccessTokenQuery
	{
		return new UserAccessTokenQuery(get_called_class());
	}
}
