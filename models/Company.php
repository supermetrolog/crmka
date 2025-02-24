<?php

namespace app\models;

use app\behaviors\CreateManyMiniModelsBehaviors;
use app\helpers\StringHelper;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\CallQuery;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ActiveQuery\CompanyQuery;
use app\models\ActiveQuery\ContactQuery;
use app\models\ActiveQuery\MediaQuery;
use app\models\ActiveQuery\OfferMixQuery;
use app\models\ActiveQuery\RelationQuery;
use app\models\ActiveQuery\RequestQuery;
use app\models\miniModels\CompanyFile;
use Yii;
use yii\base\ErrorException;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "company".
 *
 * @property int                           $id
 * @property string|null                   $nameEng
 * @property string|null                   $nameRu
 * @property string|null                   $nameBrand
 * @property int|null                      $noName
 * @property int|null                      $formOfOrganization
 * @property int|null                      $companyGroup_id
 * @property string|null                   $officeAdress
 * @property int|null                      $status
 * @property int                           $consultant_id
 * @property int|null                      $broker_id
 * @property string|null                   $legalAddress
 * @property string|null                   $ogrn
 * @property string|null                   $inn
 * @property string|null                   $kpp
 * @property string|null                   $checkingAccount
 * @property string|null                   $correspondentAccount
 * @property string|null                   $inTheBank
 * @property string|null                   $bik
 * @property string|null                   $okved
 * @property string|null                   $okpo
 * @property string|null                   $signatoryName
 * @property string|null                   $signatoryMiddleName
 * @property string|null                   $signatoryLastName
 * @property string|null                   $basis
 * @property string|null                   $documentNumber
 * @property ?int                          $activityGroup
 * @property ?int                          $activityProfile
 * @property int                           $active
 * @property int|null                      $processed
 * @property int                           $rating
 * @property string|null                   $description
 * @property int|null                      $passive_why
 * @property string|null                   $passive_why_comment
 * @property string|null                   $created_at
 * @property string|null                   $updated_at
 * @property string|null                   $latitude
 * @property string|null                   $longitude
 * @property ?int                          $media_id
 * @property bool                          $is_individual
 * @property ?string                       $individual_full_name
 *
 * @property-read ?User                    $broker
 * @property-read ?Companygroup            $companyGroup
 * @property-read User                     $consultant
 * @property-read ?Media                   $logo
 * @property-read Contact                  $mainContact
 * @property-read Contact[]                $contacts
 * @property-read Category[]               $categories
 * @property-read Productrange[]           $productRanges
 * @property-read \app\models\Objects[]    $objects
 * @property-read Request[]                $requests
 * @property-read Deal[]                   $deals
 * @property-read Deal[]                   $dealsRequestEmpty
 * @property-read CompanyFile[]            $files
 * @property-read Contact                  $generalContact
 * @property-read ChatMember               $chatMember
 * @property-read ?Call                    $lastCall
 * @property-read CompanyActivityGroup[]   $companyActivityGroups
 * @property-read CompanyActivityProfile[] $companyActivityProfiles
 */
class Company extends AR
{
	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;
	public const FORM_OF_ORGANIZATION_LIST = [
		0 => 'ООО',
		1 => 'ОАО',
		2 => 'ЗАО',
		3 => 'ПАО',
		4 => 'АО',
		5 => 'ИП',
	];

	public const LOGO_MEDIA_CATEGORY = 'company_logo';
	public const FILE_MEDIA_CATEGORY = 'company_file';

	public const COMPANY_CREATED_EVENT = 'company_created_event';
	public const COMPANY_UPDATED_EVENT = 'company_updated_event';


	public function init(): void
	{
		$this->on(self::COMPANY_CREATED_EVENT, [Yii::$app->notify, 'notifyUser']);
		parent::init();
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
	public static function tableName(): string
	{
		return 'company';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['noName', 'companyGroup_id', 'status', 'consultant_id', 'broker_id', 'activityGroup', 'activityProfile', 'formOfOrganization', 'processed', 'passive_why', 'rating'], 'integer'],
			[['consultant_id'], 'required'],
			[['description'], 'string'],
			[['is_individual'], 'boolean'],
			[['created_at', 'updated_at'], 'safe'],
			[['nameBrand', 'nameEng', 'nameRu', 'officeAdress', 'legalAddress', 'ogrn', 'inn', 'kpp', 'checkingAccount', 'correspondentAccount', 'inTheBank', 'bik', 'okved', 'okpo', 'signatoryName', 'signatoryMiddleName', 'signatoryLastName', 'basis', 'documentNumber', 'passive_why_comment', 'latitude', 'longitude', 'individual_full_name'], 'string', 'max' => 255],
			[['broker_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['broker_id' => 'id']],
			[['companyGroup_id'], 'exist', 'skipOnError' => true, 'targetClass' => Companygroup::class, 'targetAttribute' => ['companyGroup_id' => 'id']],
			[['consultant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['consultant_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id'                   => 'ID',
			'nameEng'              => 'Name Eng',
			'nameRu'               => 'Name Ru',
			'nameBrand'            => 'Name Brand',
			'noName'               => 'No Name',
			'formOfOrganization'   => 'Form Of Organization',
			'companyGroup_id'      => 'Company Group ID',
			'officeAdress'         => 'Office Adress',
			'status'               => 'Status',
			'consultant_id'        => 'Consultant ID',
			'broker_id'            => 'Broker ID',
			'legalAddress'         => 'Legal Address',
			'ogrn'                 => 'Ogrn',
			'inn'                  => 'Inn',
			'kpp'                  => 'Kpp',
			'checkingAccount'      => 'Checking Account',
			'correspondentAccount' => 'Correspondent Account',
			'inTheBank'            => 'In The Bank',
			'bik'                  => 'Bik',
			'okved'                => 'Okved',
			'okpo'                 => 'Okpo',
			'signatoryName'        => 'Signatory Name',
			'signatoryMiddleName'  => 'Signatory Middle Name',
			'signatoryLastName'    => 'Signatory Last Name',
			'basis'                => 'Basis',
			'documentNumber'       => 'Document Number',
			'activityGroup'        => 'Activity Group',
			'activityProfile'      => 'Activity Profile',
			'processed'            => 'Processed',
			'rating'               => 'Rating',
			'description'          => 'Description',
			'created_at'           => 'Created At',
			'updated_at'           => 'Updated At',
			'passive_why'          => 'PassiveWhy',
			'passive_why_comment'  => 'PassiveWhyComment',
			'latitude'             => 'Latitude',
			'longitude'            => 'Longitude',
			'is_individual'        => 'Individual Person',
			'individual_full_name' => 'Individual Full Name',
		];
	}

	public function getFullName(): string
	{
		$formOfOrganization = $this->formOfOrganization;
		$englishName        = $this->nameEng;
		$russianName        = $this->nameRu;
		$brand              = $this->nameBrand;
		$withoutName        = $this->noName;

		if ($withoutName) {
			return '-';
		}

		$name = StringHelper::join(
			StringHelper::SYMBOL_SPACE,
			!is_null($formOfOrganization) ? self::FORM_OF_ORGANIZATION_LIST[$formOfOrganization] : '',
			$russianName ?? ''
		);

		if ($englishName) {
			$name = StringHelper::join(
				$russianName ? ' - ' : ' ',
				$name,
				$englishName
			);
		}

		if ($brand) {
			$name = StringHelper::join(
				StringHelper::isNotEmpty($name) ? ' - ' : ' ',
				$name,
				$brand
			);
		}

		return StringHelper::trim($name);
	}

	public function fields(): array
	{
		$fields = parent::fields();

		$fields['full_name'] = fn() => $this->getFullName();
		$fields['logo']      = fn() => $this->logo ? $this->logo->src : null;

		return $fields;
	}


	/**
	 * @return array
	 */
	public function extraFields(): array
	{
		$extraFields = parent::extraFields();

		$extraFields['contacts_count']        = fn() => (int)$this->getContacts()->count();
		$extraFields['active_contacts_count'] = fn() => (int)$this->getActiveContacts()->count();
		$extraFields['requests_count']        = fn() => (int)$this->getRequests()->count();
		$extraFields['objects_count']         = fn() => (int)$this->getObjects()->count();

		$extraFields['offers_count'] = function ($efields) {
			$offers = $efields->getOffers()->where(['c_industry_offers_mix.deleted' => 0, 'c_industry_offers_mix.type_id' => 2])->all();

			return count($offers);
		};

		return $extraFields;
	}

	/**
	 * Gets query for [[Broker]].
	 *
	 * @return ActiveQuery
	 */
	public function getBroker(): ActiveQuery
	{
		return $this->hasOne(User::class, ['id' => 'broker_id']);
	}

	/**
	 * Gets query for [[CompanyGroup]].
	 *
	 * @return ActiveQuery
	 */
	public function getCompanyGroup(): ActiveQuery
	{
		return $this->hasOne(Companygroup::class, ['id' => 'companyGroup_id']);
	}

	/**
	 * Gets query for [[Consultant]].
	 *
	 * @return ActiveQuery
	 */
	public function getConsultant(): ActiveQuery
	{
		return $this->hasOne(User::class, ['id' => 'consultant_id']);
	}

	/**
	 * Gets query for [[Files]].
	 *
	 * @return ActiveQuery
	 */
	public function getFiles(): ActiveQuery
	{
		return $this->hasMany(CompanyFile::class, ['company_id' => 'id']);
	}

	/**
	 * @throws ErrorException
	 */
	public function getContacts(): ActiveQuery
	{
		return $this->hasMany(Contact::class, ['company_id' => 'id'])
		            ->andOnCondition([Contact::field('type') => Contact::DEFAULT_CONTACT_TYPE]);
	}

	/**
	 * @throws ErrorException
	 */
	public function getActiveContacts(): ContactQuery
	{
		/** @var ContactQuery */
		return $this->getContacts()->andOnCondition([Contact::field('status') => Contact::STATUS_ACTIVE]);
	}

	/**
	 * Gets query for [[MainContact]].
	 *
	 * @return ActiveQuery
	 */
	public function getMainContact(): ActiveQuery
	{
		return $this->hasOne(Contact::class, ['company_id' => 'id'])
		            ->where(['contact.type' => Contact::DEFAULT_CONTACT_TYPE, 'contact.isMain' => Contact::IS_MAIN_CONTACT]);
	}

	/**
	 * Gets query for [[Deals]].
	 *
	 * @return ActiveQuery
	 */
	public function getDeals(): ActiveQuery
	{
		return $this->hasMany(Deal::class, ['company_id' => 'id'])
		            ->andWhere(['!=', 'status', Deal::STATUS_DELETED]);
	}

	/**
	 * Gets query for [[Deals]].
	 *
	 * @return ActiveQuery
	 */
	public function getDealsRequestEmpty(): ActiveQuery
	{
		return $this->hasMany(Deal::class, ['company_id' => 'id'])
		            ->where(['is', 'request_id', new Expression('null')])
		            ->andWhere(['!=', 'status', Deal::STATUS_DELETED]);
	}

	/**
	 * Gets query for [[categories]].
	 *
	 * @return ActiveQuery
	 */
	public function getCategories(): ActiveQuery
	{
		return $this->hasMany(Category::class, ['company_id' => 'id']);
	}

	/**
	 * Gets query for [[productRanges]].
	 *
	 * @return ActiveQuery
	 */
	public function getProductRanges(): ActiveQuery
	{
		return $this->hasMany(Productrange::class, ['company_id' => 'id']);
	}


	/**
	 * @throws ErrorException
	 */
	public function getObjects(): ActiveQuery
	{
		return $this->hasMany(Objects::class, ['company_id' => 'id'])->from(Objects::getTable());
	}

	/**
	 * Gets query for [[OfferMix]].
	 *
	 * @return OfferMixQuery
	 */
	public function getOffers(): OfferMixQuery
	{
		/** @var OfferMixQuery $query */
		$query = $this->hasMany(OfferMix::class, ['company_id' => 'id']);

		return $query;
	}

	public function getRequests(): RequestQuery
	{
		/** @var RequestQuery */
		return $this->hasMany(Request::class, ['company_id' => 'id']);
	}

	/**
	 * @return MediaQuery
	 */
	public function getLogo(): MediaQuery
	{
		/** @var MediaQuery $query */
		$query = $this->hasOne(Media::class, ['model_id' => 'id']);

		$query->notDeleted()
		      ->byModelType(self::getMorphClass())
		      ->byCategory(self::LOGO_MEDIA_CATEGORY);

		return $query;
	}

	public function getLogoUrl(): ?string
	{
		return $this->logo ? $this->logo->src : null;
	}

	/**
	 * @throws ErrorException
	 */
	public function getGeneralContact(): ContactQuery
	{
		/** @var ContactQuery */
		return $this->hasOne(Contact::class, ['company_id' => 'id'])->andOnCondition([Contact::field('type') => Contact::GENERAL_CONTACT_TYPE]);
	}

	/**
	 * @return ChatMemberQuery|ActiveQuery
	 * @throws ErrorException
	 */
	public function getChatMember(): ChatMemberQuery
	{
		return $this->morphHasOne(ChatMember::class);
	}

	/**
	 * @throws ErrorException
	 */
	public function getLastCallRelationFirst(): RelationQuery
	{
		/** @var RelationQuery */
		return $this->hasOne(Relation::class, [
			'id' => 'last_call_rel_id'
		])->from([Relation::tableName() => Relation::getTable()]);
	}

	/**
	 * @throws ErrorException
	 */
	public function getLastCall(): CallQuery
	{
		/** @var CallQuery */
		return $this->morphHasOneVia(Call::class, 'id', 'second')
		            ->via('lastCallRelationFirst');
	}

	public function getCompanyActivityGroups(): AQ
	{
		/** @var AQ */
		return $this->hasMany(CompanyActivityGroup::class, ['company_id' => 'id']);
	}

	public function getCompanyActivityProfiles(): AQ
	{
		/** @var AQ */
		return $this->hasMany(CompanyActivityProfile::class, ['company_id' => 'id']);
	}


	public static function find(): CompanyQuery
	{
		return new CompanyQuery(static::class);
	}
}
