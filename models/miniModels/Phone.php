<?php

namespace app\models\miniModels;

use app\enum\Phone\PhoneStatusEnum;
use app\helpers\PhoneHelper;
use app\helpers\StringHelper;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\CallQuery;
use app\models\ActiveQuery\ContactQuery;
use app\models\ActiveQuery\PhoneQuery;
use app\models\Call;
use app\models\Contact;
use app\traits\EnumAttributeLabelTrait;

/**
 * This is the model class for table "phone".
 *
 * @property int          $id
 * @property int          $contact_id
 * @property string       $phone
 * @property ?string      $exten
 * @property string       $type
 * @property ?string      $comment
 * @property string       $country_code
 * @property string       $status
 * @property ?int         $isMain
 * @property string       $created_at
 * @property string       $updated_at
 * @property ?string      $deleted_at
 *
 * @property-read Contact $contact
 * @property-read Call[]  $calls
 */
class Phone extends AR
{
	use EnumAttributeLabelTrait;

	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;
	protected bool $useSoftCreate = true;

	public const MAIN_COLUMN = 'phone';

	public static function tableName(): string
	{
		return 'phone';
	}

	public function rules(): array
	{
		return [
			[['contact_id', 'phone'], 'required'],
			[['contact_id', 'isMain'], 'integer'],
			[['phone', 'exten'], 'string', 'max' => 255],
			['comment', 'string', 'max' => 128],
			['country_code', 'string', 'max' => 3],
			[['type', 'status'], 'string', 'max' => 16],
			['status', EnumValidator::class, 'enumClass' => PhoneStatusEnum::class],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
			[['contact_id'], 'exist', 'targetClass' => Contact::class, 'targetAttribute' => ['contact_id' => 'id']],
		];
	}

	public static function find(): PhoneQuery
	{
		return (new PhoneQuery(self::class))->notDeleted();
	}

	/**
	 * @deprecated Use PhoneHelper::isValidNumber
	 */
	public static function isValidPhoneNumber(string $number): bool
	{
		if (StringHelper::length($number) !== 11) {
			return false;
		}

		if (!StringHelper::isOnlyDigits($number)) {
			return false;
		}

		if (StringHelper::first($number) !== "7") {
			return false;
		}

		return true;
	}

	/** TODO: Вынести потом все в сервис, избавиться от этих createManyMiniModels */
	public function beforeSave($insert): bool
	{
		parent::beforeSave($insert);

		$this->phone = StringHelper::extractDigits($this->phone);

		return true;
	}

	public function isMainPhone(): bool
	{
		return $this->isMain === 1;
	}

	public function isActive(): bool
	{
		return $this->status === PhoneStatusEnum::ACTIVE;
	}

	public function isPassive(): bool
	{
		return $this->status === PhoneStatusEnum::PASSIVE;
	}

	public function getStatusLabel(): string
	{
		return $this->getEnumLabel('status', PhoneStatusEnum::class);
	}

	public function getContact(): ContactQuery
	{
		/** @var ContactQuery */
		return $this->hasOne(Contact::class, ['id' => 'contact_id']);
	}

	public function getCalls(): CallQuery
	{
		/** @var CallQuery */
		return $this->hasMany(Call::class, ['phone_id' => 'id']);
	}
}
