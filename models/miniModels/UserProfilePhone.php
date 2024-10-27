<?php

namespace app\models\miniModels;

use app\helpers\StringHelper;
use app\kernel\common\models\AR\AR;
use app\models\UserProfile;
use floor12\phone\PhoneFormatter;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user_profile_phone".
 *
 * @property int         $id
 * @property int         $user_profile_id [СВЯЗЬ] с профилем юзера
 * @property string      $phone           номер телефона
 *
 * @property UserProfile $userProfile
 */
class UserProfilePhone extends AR
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'user_profile_phone';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['user_profile_id', 'phone'], 'required'],
			[['user_profile_id'], 'integer'],
			[['phone'], 'string', 'max' => 255],
			[['user_profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserProfile::class, 'targetAttribute' => ['user_profile_id' => 'id']],
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
			'phone'           => 'Phone',
		];
	}

	public function toFormattedPhone(): string
	{
		if (Phone::isValidPhoneNumber($this->phone)) {
			return PhoneFormatter::format($this->phone);
		}

		return $this->phone;
	}


	public function beforeSave($insert): bool
	{
		parent::beforeSave($insert);

		$this->phone = StringHelper::extractDigits($this->phone);

		return true;
	}

	// TODO: Удалить, когда переведем все на ресурсы
	public function fields(): array
	{
		$fields = parent::fields();

		$fields['phone'] = fn() => $this->toFormattedPhone();

		return $fields;
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
