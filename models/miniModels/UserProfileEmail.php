<?php

namespace app\models\miniModels;

use app\kernel\common\models\AR\AR;
use app\models\User\UserProfile;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user_profile_email".
 *
 * @property int         $id
 * @property int         $user_profile_id [СВЯЗЬ] с профилем юзера
 * @property string      $email           email
 *
 * @property UserProfile $userProfile
 */
class UserProfileEmail extends AR
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'user_profile_email';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['user_profile_id', 'email'], 'required'],
			[['user_profile_id'], 'integer'],
			[['email'], 'string', 'max' => 255],
			[['user_profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserProfile::className(), 'targetAttribute' => ['user_profile_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array
	{
		return [
			'id'              => 'ID',
			'user_profile_id' => 'User Profile ID',
			'email'           => 'Email',
		];
	}

	/**
	 * Gets query for [[UserProfile]].
	 *
	 * @return ActiveQuery
	 */
	public function getUserProfile(): ActiveQuery
	{
		return $this->hasOne(UserProfile::class, ['id' => 'user_profile_id']);
	}
}
