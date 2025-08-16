<?php

namespace app\models;

use app\behaviors\CreateManyMiniModelsBehaviors;
use app\helpers\ArrayHelper as AppArrayHelper;
use app\helpers\PersonNameHelper;
use app\helpers\StringHelper;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\CallQuery;
use app\models\ActiveQuery\ContactQuery;
use app\models\ActiveQuery\PhoneQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\miniModels\ContactComment;
use app\models\miniModels\Email;
use app\models\miniModels\Phone;
use app\models\miniModels\WayOfInforming;
use app\models\miniModels\Website;
use app\resources\Contact\Email\ContactEmailResource;
use app\resources\Phone\PhoneResource;
use yii\base\ErrorException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "contact".
 *
 * @property int                   $id
 * @property int                   $company_id
 * @property string                $first_name
 * @property string|null           $middle_name
 * @property string|null           $last_name
 * @property int|null              $status
 * @property int|null              $type
 * @property string|null           $created_at
 * @property string|null           $updated_at
 * @property int|null              $consultant_id     [связь] с пользователями
 * @property int|null              $position          Должность
 * @property int|null              $position_unknown  Должность неизвестна
 * @property int|null              $faceToFaceMeeting [флаг] Очная встреча
 * @property int|null              $warning           [флаг] Внимание
 * @property int|null              $good              [флаг] Хор. взаимоотношения
 * @property int|null              $passive_why
 * @property string|null           $passive_why_comment
 * @property string|null           $warning_why_comment
 * @property int|null              $isMain            основной контакт
 * @property-read Company          $company
 * @property-read User             $consultant
 * @property-read ContactComment[] $contactComments
 * @property-read Email[]          $emails
 * @property-read Phone[]          $phones
 * @property-read WayOfInforming[] $wayOfInformings
 * @property-read null|string      $shortName
 * @property-read null|string      $fullName
 * @property-read ActiveQuery      $relatedContacts
 * @property-read Website[]        $websites
 * @property-read Call[]           $calls
 */
class Contact extends AR
{
	public const PASSIVE_WHY_PHONES_NOT_ACTUAL      = 0;
	public const PASSIVE_WHY_NOT_WORKING_IN_COMPANY = 1;
	public const PASSIVE_WHY_BLOCKED                = 2;
	public const PASSIVE_WHY_OTHER                  = 3;
	public const PASSIVE_WHY_COMPANY_DISABLED       = 4;
	public const PASSIVE_WHY_NOT_EXISTS             = 5;

	public static function getPassiveWhyOptions(): array
	{
		return [
			self::PASSIVE_WHY_PHONES_NOT_ACTUAL,
			self::PASSIVE_WHY_NOT_WORKING_IN_COMPANY,
			self::PASSIVE_WHY_BLOCKED,
			self::PASSIVE_WHY_OTHER,
			self::PASSIVE_WHY_COMPANY_DISABLED,
			self::PASSIVE_WHY_NOT_EXISTS,
		];
	}

	public const passiveWhyMap = [
		self::PASSIVE_WHY_PHONES_NOT_ACTUAL      => 'Телефоны не актуальны',
		self::PASSIVE_WHY_NOT_WORKING_IN_COMPANY => 'Не работает в компании',
		self::PASSIVE_WHY_BLOCKED                => 'Заблокировано модератором',
		self::PASSIVE_WHY_OTHER                  => 'Другое',
		self::PASSIVE_WHY_COMPANY_DISABLED       => 'Компания архивирована',
		self::PASSIVE_WHY_NOT_EXISTS             => 'Номер не существует',
	];

	public static function resolvePassiveWhyOption(?int $code): string
	{
		return self::passiveWhyMap[$code] ?? 'Причина не указана';
	}

	public const GENERAL_CONTACT_FIRST_NAME = 'Общий контакт';
	public const GENERAL_CONTACT_TYPE       = 1;
	public const DEFAULT_CONTACT_TYPE       = 0;
	public const IS_MAIN_CONTACT            = 1;
	public const LIST_CONTACT_TYPE          = 0;

	public const STATUS_PASSIVE = 0;
	public const STATUS_ACTIVE  = 1;

	public const POSITION_IS_KNOWN   = 0;
	public const POSITION_IS_UNKNOWN = 1;

	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;

	public function behaviors(): array
	{
		return [
			CreateManyMiniModelsBehaviors::class
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'contact';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['company_id'], 'required'],
			[['company_id', 'status', 'type', 'consultant_id', 'position', 'faceToFaceMeeting', 'warning', 'good', 'passive_why', 'position_unknown', 'isMain'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['first_name', 'middle_name', 'last_name', 'passive_why_comment', 'warning_why_comment'], 'string', 'max' => 255],
			[['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
			[['consultant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['consultant_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array
	{
		return [
			'id'                  => 'ID',
			'company_id'          => 'Company ID',
			'first_name'          => 'First Name',
			'middle_name'         => 'Middle Name',
			'last_name'           => 'Last Name',
			'status'              => 'Status',
			'type'                => 'Type',
			'created_at'          => 'Created At',
			'updated_at'          => 'Updated At',
			'consultant_id'       => 'Consultant ID',
			'position'            => 'Position',
			'faceToFaceMeeting'   => 'Face To Face Meeting',
			'warning'             => 'Warning',
			'good'                => 'Good',
			'passive_why'         => 'PassiveWhy',
			'passive_why_comment' => 'PassiveWhyComment',
			'warning_why_comment' => 'WarningWhyComment',
			'position_unknown'    => 'PositionUnknown',
			'isMain'              => 'IsMain'
		];
	}

	public function getFullName(): ?string
	{
		$fullName = PersonNameHelper::generateFullName(
			$this->first_name ?? "",
			$this->middle_name ?? "",
			$this->last_name ?? ""
		);

		return $fullName ?: null;
	}

	public function getShortName(): ?string
	{
		$shortName = PersonNameHelper::generateShortName(
			$this->first_name ?? "",
			$this->middle_name ?? "",
			$this->last_name ?? ""
		);

		return $shortName ?: null;
	}

	public function getFirstAndLastName(): ?string
	{
		$name = StringHelper::join(
			StringHelper::SYMBOL_SPACE,
			$fs->first_name ?? "",
			$fs->last_name ?? ""
		);

		return $name ?: null;
	}

	// TODO: Удалить после того как перейдем на Resource
	public function fields(): array
	{
		$fields = parent::fields();

		$fields['full_name']  = fn() => $this->getFullName();
		$fields['short_name'] = fn() => $this->getShortName();

		$fields['first_and_last_name'] = fn() => $this->getFirstAndLastName();

		$fields['updated_at'] = static function ($fs) {
			return $fs->updated_at === "0000-00-00 00:00:00" ? null : $fs->updated_at;
		};

		$fields['created_at'] = static function ($fs) {
			return $fs->created_at === "0000-00-00 00:00:00" ? null : $fs->created_at;
		};

		return $fields;
	}

	// TODO: Удалить после того как перейдем на Resource
	public function extraFields()
	{
		$extraFields = parent::extraFields();

		$extraFields['phones'] = static function ($ef) {
			$resource = AppArrayHelper::filter($ef['phones'], static function ($phone) {
				$phone = $phone->toArray();

				return Phone::isValidPhoneNumber($phone['phone']);
			});

			return PhoneResource::collection($resource);
		};

		$extraFields['invalidPhones'] = static function ($ef) {
			$resource = AppArrayHelper::filter($ef['phones'], static function ($phone) {
				$phone = $phone->toArray();

				return !Phone::isValidPhoneNumber($phone['phone']);
			});

			return PhoneResource::collection($resource);
		};

		$extraFields['emails'] = static function ($ef) {
			return ContactEmailResource::collection($ef['emails']);
		};

		return $extraFields;
	}

	/**
	 * Gets query for [[Company]].
	 *
	 * @return ActiveQuery
	 */
	public function getCompany(): ActiveQuery
	{
		return $this->hasOne(Company::class, ['id' => 'company_id']);
	}

	/**
	 * Gets query for [[Consultant]].
	 *
	 * @return UserQuery
	 */
	public function getConsultant(): UserQuery
	{
		/** @var UserQuery $query */
		$query = $this->hasOne(User::class, ['id' => 'consultant_id']);

		return $query;
	}

	/**
	 * Gets query for [[ContactComments]].
	 *
	 * @return ActiveQuery
	 * @throws ErrorException
	 */
	public function getContactComments(): ActiveQuery
	{
		return $this->hasMany(ContactComment::class, ['contact_id' => 'id'])->andOnCondition([ContactComment::field('deleted_at') => null]);
	}

	/**
	 * Gets query for [[Emails]].
	 *
	 * @return ActiveQuery
	 */
	public function getEmails(): ActiveQuery
	{
		return $this->hasMany(Email::class, ['contact_id' => 'id']);
	}

	public function getPhones(): PhoneQuery
	{
		/** @var PhoneQuery */
		return $this->hasMany(Phone::class, ['contact_id' => 'id']);
	}

	/**
	 * Gets query for [[WayOfInformings]].
	 *
	 * @return ActiveQuery
	 */
	public function getWayOfInformings(): ActiveQuery
	{
		return $this->hasMany(WayOfInforming::class, ['contact_id' => 'id']);
	}

	/**
	 * Gets query for [[Websites]].
	 *
	 * @return ActiveQuery
	 */
	public function getWebsites(): ActiveQuery
	{
		return $this->hasMany(Website::class, ['contact_id' => 'id']);
	}

	public function getRelatedContacts(): ContactQuery
	{
		/** @var ContactQuery */
		return $this->hasMany(__CLASS__, ['company_id' => 'company_id'])->andWhere(['!=', 'id', $this->id]);
	}

	/**
	 * @throws ErrorException
	 */
	public function getCalls(): CallQuery
	{
		/** @var CallQuery */
		return $this->hasMany(Call::class, ['contact_id' => 'id'])->andOnCondition([Call::field('deleted_at') => null]);
	}

	public static function find(): ContactQuery
	{
		return new ContactQuery(get_called_class());
	}

	public function isPassive(): bool
	{
		return $this->status === self::STATUS_PASSIVE;
	}

	public function isActive(): bool
	{
		return $this->status === self::STATUS_ACTIVE;
	}
}
