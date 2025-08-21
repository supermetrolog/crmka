<?php

namespace app\models;

use app\behaviors\CreateManyMiniModelsBehaviors;
use app\enum\Phone\PhoneCountryCodeEnum;
use app\exceptions\ValidationErrorHttpException;
use app\helpers\ArrayHelper;
use app\helpers\PhoneHelper;
use app\helpers\StringHelper;
use app\kernel\common\models\AR\AR;
use app\models\miniModels\UserProfileEmail;
use app\models\miniModels\UserProfilePhone;
use libphonenumber\PhoneNumberFormat;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user_profile".
 *
 * @property int                     $id
 * @property int                     $user_id   [связь] с юзером
 * @property string|null             $first_name
 * @property string|null             $middle_name
 * @property string|null             $last_name
 * @property string|null             $caller_id Номер в системе Asterisk
 * @property string|null             $avatar
 *
 * @property string                  $fullName
 * @property string                  $shortName
 * @property string                  $mediumName
 *
 * @property CallList[]              $callLists
 * @property User                    $user
 *
 * @property-read UserProfileEmail[] $emails
 * @property-read UserProfilePhone[] $phones
 */
class UserProfile extends AR
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'user_profile';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['user_id'], 'required'],
			[['user_id'], 'integer'],
			[['first_name', 'middle_name', 'last_name', 'caller_id', 'avatar'], 'string', 'max' => 255],
			[['caller_id'], 'unique'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function behaviors(): array
	{
		return [
			CreateManyMiniModelsBehaviors::class
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array
	{
		return [
			'id'          => 'ID',
			'user_id'     => 'User ID',
			'first_name'  => 'First Name',
			'middle_name' => 'Middle Name',
			'last_name'   => 'Last Name',
			'caller_id'   => 'Caller ID',
			'avatar'      => "Avatar",
		];
	}

	// TODO: Вынести в сервис и заменить на Media
	public function uploadFiles($uploadFileModel, UserProfile $model)
	{
		foreach ($uploadFileModel->files as $file) {
			if (!$uploadFileModel->uploadOne($file)) {
				throw new ValidationErrorHttpException($uploadFileModel->getErrorSummary(false));
			}
			$model->avatar = $uploadFileModel->filename;
		}

		return $model;
	}

	public function getFullName(): string
	{
		return StringHelper::join(
			StringHelper::SYMBOL_SPACE,
			$this->middle_name ?? "",
			$this->first_name,
			$this->last_name ?? ""
		);
	}

	public function getShortName(): string
	{
		$firstNameCharacter = StringHelper::ucFirst(StringHelper::first($this->first_name));
		$lastNameCharacter  = StringHelper::ucFirst(StringHelper::first($this->last_name ?? ""));

		$characters = StringHelper::join(". ", $firstNameCharacter, $lastNameCharacter);

		return StringHelper::join(StringHelper::SYMBOL_SPACE, $this->middle_name ?? "", $characters) . ".";
	}

	public function getMediumName(): string
	{
		return StringHelper::join(
			StringHelper::SYMBOL_SPACE,
			$this->first_name,
			$this->middle_name ?? ""
		);
	}

	// TODO: Удалить, когда переведем все на ресурсы
	public function fields(): array
	{
		$fields                = parent::fields();
		$fields['full_name']   = function () {
			return $this->fullName;
		};
		$fields['short_name']  = function () {
			return $this->shortName;
		};
		$fields['medium_name'] = function () {
			return $this->mediumName;
		};

		return $fields;
	}

	public function getFormattedPhone(): string
	{
		if (ArrayHelper::notEmpty($this->phones)) {
			return PhoneHelper::tryFormat($this->phones[0]->phone, PhoneNumberFormat::NATIONAL, PhoneCountryCodeEnum::RU);
		}

		return '-';
	}

	public function getFormattedTel(): string
	{
		if (ArrayHelper::notEmpty($this->phones)) {
			return PhoneHelper::tryFormat($this->phones[0]->phone, PhoneNumberFormat::RFC3966, PhoneCountryCodeEnum::RU);
		}

		return '-';
	}

	/**
	 * Gets query for [[CallLists]].
	 *
	 * @return ActiveQuery
	 */
	public function getCallLists(): ActiveQuery
	{
		return $this->hasMany(CallList::class, ['caller_id' => 'caller_id']);
	}

	/**
	 * Gets query for [[Phones]].
	 *
	 * @return ActiveQuery
	 */
	public function getEmails(): ActiveQuery
	{
		return $this->hasMany(UserProfileEmail::class, ['user_profile_id' => 'id']);
	}

	/**
	 * Gets query for [[Phones]].
	 *
	 * @return ActiveQuery
	 */
	public function getPhones(): ActiveQuery
	{
		return $this->hasMany(UserProfilePhone::class, ['user_profile_id' => 'id']);
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return ActiveQuery
	 */
	public function getUser(): ActiveQuery
	{
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}
}
