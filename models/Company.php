<?php

namespace app\models;

use app\behaviors\CreateManyMiniModelsBehaviors;
use app\events\NotificationEvent;
use app\helpers\DbHelper;
use app\kernel\common\models\AR\AR;
use Throwable;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use app\exceptions\ValidationErrorHttpException;
use app\models\miniModels\CompanyFile;
use app\models\oldDb\Objects;
use app\models\oldDb\OfferMix;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "company".
 *
 * @property int          $id
 * @property string|null  $nameEng
 * @property string|null  $nameRu
 * @property string|null  $nameBrand
 * @property int|null     $noName
 * @property int|null     $formOfOrganization
 * @property int|null     $companyGroup_id
 * @property string|null  $officeAdress
 * @property int|null     $status
 * @property int          $consultant_id
 * @property int|null     $broker_id
 * @property string|null  $legalAddress
 * @property string|null  $ogrn
 * @property string|null  $inn
 * @property string|null  $kpp
 * @property string|null  $checkingAccount
 * @property string|null  $correspondentAccount
 * @property string|null  $inTheBank
 * @property string|null  $bik
 * @property string|null  $okved
 * @property string|null  $okpo
 * @property string|null  $signatoryName
 * @property string|null  $signatoryMiddleName
 * @property string|null  $signatoryLastName
 * @property string|null  $basis
 * @property string|null  $documentNumber
 * @property int          $activityGroup
 * @property int          $activityProfile
 * @property int/null $processed
 * @property int          $rating
 * @property string|null  $description
 * @property int|null     $passive_why
 * @property string|null  $passive_why_comment
 * @property string|null  $created_at
 * @property string|null  $updated_at
 * @property string|null  $latitude
 * @property string|null  $longitude
 *
 * @property User         $broker
 * @property Companygroup $companyGroup
 * @property User         $consultant
 */
class Company extends AR
{
	public const FORM_OF_ORGANIZATION_LIST = [
		0 => 'ООО',
		1 => 'ОАО',
		2 => 'ЗАО',
		3 => 'ПАО',
		4 => 'АО',
		5 => 'ИП',
	];

	public const COMPANY_CREATED_EVENT = 'company_created_event';
	public const COMPANY_UPDATED_EVENT = 'company_updated_event';


	public function init()
	{
		$this->on(self::COMPANY_CREATED_EVENT, [Yii::$app->notify, 'notifyUser']);
		parent::init();
	}

	public function behaviors()
	{
		return [
			CreateManyMiniModelsBehaviors::class
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'company';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['noName', 'companyGroup_id', 'status', 'consultant_id', 'broker_id', 'activityGroup', 'activityProfile', 'formOfOrganization', 'processed', 'passive_why', 'rating'], 'integer'],
			[['consultant_id', 'activityGroup', 'activityProfile'], 'required'],
			[['description'], 'string'],
			[['created_at', 'updated_at'], 'safe'],
			[['nameBrand', 'nameEng', 'nameRu', 'officeAdress', 'legalAddress', 'ogrn', 'inn', 'kpp', 'checkingAccount', 'correspondentAccount', 'inTheBank', 'bik', 'okved', 'okpo', 'signatoryName', 'signatoryMiddleName', 'signatoryLastName', 'basis', 'documentNumber', 'passive_why_comment', 'latitude', 'longitude'], 'string', 'max' => 255],
			[['broker_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['broker_id' => 'id']],
			[['companyGroup_id'], 'exist', 'skipOnError' => true, 'targetClass' => Companygroup::className(), 'targetAttribute' => ['companyGroup_id' => 'id']],
			[['consultant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['consultant_id' => 'id']],
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
			'longitude'            => 'Longitude'
		];
	}

	public function fields(): array
	{
		$fields = parent::fields();

		$fields['progress_percent']  = function () {
			return rand(10, 100);
		};

		$fields['request_count']     = function () {
			return (int)$this->getRequests()->count();
		};

		$fields['offer_count']       = function () {
			return (int)$this->getOffers()->where(['c_industry_offers_mix.deleted' => 0, 'c_industry_offers_mix.type_id' => 2])->count();
		};

		$fields['object_count']      = function () {
			return (int)$this->getObjects()->count();
		};

        $fields['deal_count']       = function () {
            return (int)$this->getDeals()->count();
        };

		$fields['created_at_format'] = function ($fields) {
			return Yii::$app->formatter->format($fields['created_at'], 'datetime');
		};
		$fields['updated_at_format'] = function ($fields) {
			return $fields['updated_at'] ? Yii::$app->formatter->format($fields['updated_at'], 'datetime') : null;
		};
		$fields['full_name']         = function ($fields) {
			$formOfOrganization = $fields['formOfOrganization'];
			$nameEng            = $fields['nameEng'];
			$nameRu             = $fields['nameRu'];
			$nameBrand          = $fields['nameBrand'];
			$noName             = $fields['noName'];
			if ($noName) {
				return "-";
			}
			$name = "";
			if ($formOfOrganization !== null) {
				$name .= self::FORM_OF_ORGANIZATION_LIST[$formOfOrganization];
			}

			if ($nameRu) {
				$name .= " $nameRu";
			}
			if ($nameEng) {
				if ($nameRu) {
					$name .= " - $nameEng";
				} else {
					$name .= " $nameEng";
				}
			}
			if ($nameBrand) {
				if ($name != "") {
					$name .= " - $nameBrand";
				} else {
					$name .= " $nameBrand";
				}
			}

			return trim($name);
		};

		return $fields;
	}


	/**
	 * @return array
	 */
	public function extraFields(): array
	{
		$extraFields = parent::extraFields();

		$extraFields['contacts_count'] = function () {
			return (int)$this->getContacts()->count();
		};
		$extraFields['requests_count'] = function () {
			return (int)$this->getRequests()->count();
		};
		$extraFields['objects_count']  = function () {
			return (int)$this->getObjects()->count();
		};

		// TODO:
		$extraFields['offers_count'] = function ($efields) {
			$offers = $efields->getOffers()->where(['c_industry_offers_mix.deleted' => 0, 'c_industry_offers_mix.type_id' => 2])->all();

			return count($offers);
		};

		return $extraFields;
	}

	public static function getCompanyList()
	{
		$dataProvider = new ActiveDataProvider([
			'query'      => self::find()->joinWith(['requests'])->with([
				'requests'                                      => function ($query) {
					$query->where(['status' => Request::STATUS_ACTIVE]);
				},
				'companyGroup', 'broker', 'deals', 'consultant' => function ($query) {
					$query->with('userProfile');
				}, 'productRanges', 'categories', 'contacts'    => function ($query) {
					$query->with(['phones', 'emails', 'contactComments']);
				}
			]),
			'pagination' => [
				'pageSize' => 0,
			],
			'sort'       => [
				'attributes' => [
					'default'            => [
						'asc'     => ['request.created_at' => SORT_ASC, 'company.rating' => SORT_ASC, 'company.created_at' => SORT_ASC],
						'desc'    => ['request.created_at' => SORT_DESC, 'company.rating' => SORT_DESC, 'company.created_at' => SORT_DESC],
						'default' => SORT_DESC,
					],
					'company.rating'     => [
						'asc'     => ['company.rating' => SORT_ASC],
						'desc'    => ['company.rating' => SORT_DESC],
						'default' => SORT_DESC,
					],
					'company.created_at' => [
						'asc'     => ['company.created_at' => SORT_ASC],
						'desc'    => ['company.created_at' => SORT_DESC],
						'default' => SORT_DESC,
					],
				],

			]
		]);

		return $dataProvider;
	}

	public static function getCompanyInfo($id)
	{
		return self::find()->with(['productRanges', 'categories', 'companyGroup', 'broker', 'deals', 'consultant'                                      => function ($query) {
			$query->with(['userProfile']);
		}, 'files', 'dealsRequestEmpty.consultant.userProfile', 'dealsRequestEmpty.offer.generalOffersMix', 'dealsRequestEmpty.competitor', 'contacts' => function ($query) {
			$query->with(['phones', 'emails', 'contactComments', 'websites']);
		}])->where(['company.id' => $id])->limit(1)->one();
	}

	private function createGeneralContact($post_data)
	{
		if (!count($post_data['phones']) && !count($post_data['emails']) && !count($post_data['websites'])) {
			return;
		}
		$post_data['company_id'] = $this->id;
		$post_data['type']       = Contact::GENERAL_CONTACT_TYPE;

		return Contact::createContact($post_data);
	}

	private function updateGeneralContact($post_data)
	{
		$model = Contact::find()->where(['company_id' => $this->id, 'type' => Contact::GENERAL_CONTACT_TYPE])->one();
		if (!$model) {
			return $this->createGeneralContact($post_data);
		}

		return Contact::updateContact($model, $post_data);
	}

	private function updateFiles($post_data, $uploadFileModel)
	{
		CompanyFile::deleteAll(['company_id' => $this->id]);
		foreach ($post_data['files'] as $file) {
			$model = new CompanyFile();
			if (!$model->load($file, '') || !$model->save()) {
				throw new ValidationErrorHttpException($model->getErrorSummary(false));
			}
		}
		$this->uploadFiles($uploadFileModel);
	}

	private function uploadFiles($uploadFileModel)
	{
		foreach ($uploadFileModel->files as $file) {
			if (!$uploadFileModel->uploadOne($file)) {
				throw new ValidationErrorHttpException($uploadFileModel->getErrorSummary(false));
			}
			$companyFileModel             = new CompanyFile();
			$companyFileModel->company_id = $this->id;
			$companyFileModel->name       = $file->name;
			$companyFileModel->type       = $file->type;
			$companyFileModel->filename   = $uploadFileModel->filename;
			$companyFileModel->size       = (string)$file->size;
			if (!$companyFileModel->save()) {
				throw new ValidationErrorHttpException($companyFileModel->getErrorSummary(false));
			}
		}
	}

	public static function createCompany($post_data, $uploadFileModel)
	{
		$db          = Yii::$app->db;
		$model       = new Company();
		$transaction = $db->beginTransaction();
		try {
			if ($model->load($post_data, '') && $model->save()) {
				$model->createManyMiniModels([
					Category::class     => $post_data['categories'],
					Productrange::class => $post_data['productRanges'],
				]);
				$model->createGeneralContact($post_data['contacts']);
				$model->uploadFiles($uploadFileModel);

				$model->trigger(Company::COMPANY_CREATED_EVENT, new NotificationEvent([
					'consultant_id' => $model->consultant_id,
					'type'          => Notification::TYPE_COMPANY_INFO,
					'title'         => 'компания',
					'body'          => Yii::$app->controller->renderFile('@app/views/notifications_template/assigned_company.php', ['model' => $model])
				]));

				$transaction->commit();

				return ['message' => "Компания создана", 'data' => $model->id];
			}
			throw new ValidationErrorHttpException($model->getErrorSummary(false));
		} catch (Throwable $th) {
			$transaction->rollBack();
			throw $th;
		}
	}

	public static function updateCompany(Company $model, $post_data, $uploadFileModel = [])
	{
		$db              = Yii::$app->db;
		$transaction     = $db->beginTransaction();
		$oldConsultantId = $model->consultant_id;
		try {
			$post_data['updated_at'] = date('Y-m-d H:i:s');
			if ($model->load($post_data, '') && $model->save()) {
				$model->updateManyMiniModels([
					Category::class     => $post_data['categories'],
					Productrange::class => $post_data['productRanges'],
				]);
				$model->updateGeneralContact($post_data['contacts']);
				$model->updateFiles($post_data, $uploadFileModel);
				// $transaction->rollBack();
				if ($oldConsultantId != $model->consultant_id) {
					$model->trigger(Company::COMPANY_CREATED_EVENT, new NotificationEvent([
						'consultant_id' => $oldConsultantId,
						'type'          => Notification::TYPE_COMPANY_INFO,
						'title'         => 'компания',
						'body'          => Yii::$app->controller->renderFile('@app/views/notifications_template/unAssigned_company.php', ['model' => $model])
					]));
					$model->trigger(Company::COMPANY_CREATED_EVENT, new NotificationEvent([
						'consultant_id' => $model->consultant_id,
						'type'          => Notification::TYPE_COMPANY_INFO,
						'title'         => 'компания',
						'body'          => Yii::$app->controller->renderFile('@app/views/notifications_template/assigned_company.php', ['model' => $model])
					]));
				}
				$transaction->commit();

				return ['message' => "Компания изменена", 'data' => $model->id];
			}
			throw new ValidationErrorHttpException($model->getErrorSummary(false));
		} catch (Throwable $th) {
			$transaction->rollBack();
			throw $th;
		}
	}

	/**
	 * Gets query for [[Broker]].
	 *
	 * @return ActiveQuery
	 */
	public function getBroker()
	{
		return $this->hasOne(User::className(), ['id' => 'broker_id']);
	}

	/**
	 * Gets query for [[CompanyGroup]].
	 *
	 * @return ActiveQuery
	 */
	public function getCompanyGroup()
	{
		return $this->hasOne(Companygroup::className(), ['id' => 'companyGroup_id']);
	}

	/**
	 * Gets query for [[Consultant]].
	 *
	 * @return ActiveQuery
	 */
	public function getConsultant()
	{
		return $this->hasOne(User::className(), ['id' => 'consultant_id']);
	}

	/**
	 * Gets query for [[Files]].
	 *
	 * @return ActiveQuery
	 */
	public function getFiles()
	{
		return $this->hasMany(CompanyFile::className(), ['company_id' => 'id']);
	}

	/**
	 * Gets query for [[Contacts]].
	 *
	 * @return ActiveQuery
	 */
	public function getContacts()
	{
		return $this->hasMany(Contact::className(), ['company_id' => 'id']);
	}

	/**
	 * Gets query for [[MainContact]].
	 *
	 * @return ActiveQuery
	 */
	public function getMainContact(): ActiveQuery
	{
		return $this->hasOne(Contact::className(), ['company_id' => 'id'])
		            ->where(['contact.type' => Contact::DEFAULT_CONTACT_TYPE, 'contact.isMain' => Contact::IS_MAIN_CONTACT]);
	}

	/**
	 * Gets query for [[Deals]].
	 *
	 * @return ActiveQuery
	 */
	public function getDeals()
	{
		return $this->hasMany(Deal::className(), ['company_id' => 'id'])->andWhere(['!=', 'status', Deal::STATUS_DELETED]);
	}

	/**
	 * Gets query for [[Deals]].
	 *
	 * @return ActiveQuery
	 */
	public function getDealsRequestEmpty()
	{
		return $this->hasMany(Deal::className(), ['company_id' => 'id'])->where(['is', 'request_id', new Expression('null')])->andWhere(['!=', 'status', Deal::STATUS_DELETED]);
	}

	/**
	 * Gets query for [[categories]].
	 *
	 * @return ActiveQuery
	 */
	public function getCategories()
	{
		return $this->hasMany(Category::className(), ['company_id' => 'id']);
	}

	/**
	 * Gets query for [[productRanges]].
	 *
	 * @return ActiveQuery
	 */
	public function getProductRanges()
	{
		return $this->hasMany(Productrange::className(), ['company_id' => 'id']);
	}


	/**
	 * Gets query for [[Objects]].
	 *
	 * @return ActiveQuery
	 */
	public function getObjects()
	{
		return $this->hasMany(Objects::className(), ['company_id' => 'id']);
	}

	/**
	 * Gets query for [[OfferMix]].
	 *
	 * @return ActiveQuery
	 */
	public function getOffers()
	{
		return $this->hasMany(OfferMix::className(), ['company_id' => 'id']);
	}

	/**
	 * Gets query for [[Contacts]].
	 *
	 * @return ActiveQuery
	 */
	public function getRequests()
	{
		return $this->hasMany(Request::className(), ['company_id' => 'id']);
	}
}
