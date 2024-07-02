<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\ContactQuery;
use app\models\ActiveQuery\EquipmentQuery;
use app\models\ActiveQuery\MediaQuery;
use app\models\ActiveQuery\RelationQuery;
use app\models\ActiveQuery\UserQuery;
use yii\base\ErrorException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "equipment".
 *
 * @property int         $id
 * @property string|null $name
 * @property string|null $address
 * @property string|null $description
 * @property int         $company_id
 * @property int         $contact_id
 * @property int         $consultant_id
 * @property int         $preview_id
 * @property int|null    $category
 * @property int|null    $availability
 * @property int|null    $delivery
 * @property int|null    $deliveryPrice
 * @property int|null    $price
 * @property int|null    $benefit
 * @property int|null    $tax
 * @property int|null    $count
 * @property int|null    $state
 * @property int|null    $status
 * @property int|null    $passive_type
 * @property string|null $passive_comment
 * @property string|null $archived_at
 * @property string      $created_by_type
 * @property int         $created_by_id
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 *
 * @property Company     $company
 * @property User        $consultant
 * @property Contact     $contact
 * @property Media       $preview
 * @property Media[]     $files
 * @property Media[]     $photos
 */
class Equipment extends AR
{
	public const CATEGORY_RACKING               = 1;
	public const CATEGORY_MACHINE_TOOLS         = 2;
	public const CATEGORY_CRANE                 = 3;
	public const CATEGORY_LIFTING               = 4;
	public const CATEGORY_LOADING_AND_UNLOADING = 5;
	public const CATEGORY_SERVER                = 6;
	public const CATEGORY_OTHER                 = 7;

	public const AVAILABILITY_STOCK = 1;
	public const AVAILABILITY_ORDER = 2;

	public const DELIVERY_PAY      = 1;
	public const DELIVERY_IN_PRICE = 2;
	public const DELIVERY_PICKUP   = 3;

	public const BENEFIT_YES      = 1;
	public const BENEFIT_STANDARD = 2;

	public const TAX_VAT = 1;
	public const TAX_NO  = 2;

	public const STATE_NEW    = 1;
	public const STATE_USED   = 2;
	public const STATE_BROKEN = 3;

	public const STATUS_ACTIVE  = 1;
	public const STATUS_PASSIVE = 2;

	public const PASSIVE_TYPE_SOLD      = 1;
	public const PASSIVE_TYPE_OUTDATED  = 2;
	public const PASSIVE_TYPE_CANCELED  = 3;
	public const PASSIVE_TYPE_MODERATOR = 4;
	public const PASSIVE_TYPE_OTHER     = 5;

	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;

	public static function tableName(): string
	{
		return 'equipment';
	}

	public function rules(): array
	{
		return [
			[['description', 'passive_comment'], 'string'],
			[['company_id', 'contact_id', 'consultant_id', 'preview_id', 'created_by_type', 'created_by_id'], 'required'],
			[['company_id', 'contact_id', 'consultant_id', 'preview_id', 'category', 'availability', 'delivery', 'deliveryPrice', 'price', 'benefit', 'tax', 'count', 'state', 'status', 'passive_type', 'created_by_id'], 'integer'],
			[['archived_at', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
			[['name'], 'string', 'max' => 60],
			[['address', 'created_by_type'], 'string', 'max' => 255],
			['category', 'in', 'range' => self::getCategories()],
			['availability', 'in', 'range' => self::getAvailabilities()],
			['delivery', 'in', 'range' => self::getDeliveries()],
			['benefit', 'in', 'range' => self::getBenefits()],
			['tax', 'in', 'range' => self::getTaxes()],
			['state', 'in', 'range' => self::getStates()],
			['status', 'in', 'range' => self::getStatuses()],
			['passive_type', 'in', 'range' => self::getPassiveTypes()],
			[['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
			[['consultant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['consultant_id' => 'id']],
			[['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],
			[['preview_id'], 'exist', 'skipOnError' => true, 'targetClass' => Media::className(), 'targetAttribute' => ['preview_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'              => 'ID',
			'name'            => 'Name',
			'address'         => 'Address',
			'description'     => 'Description',
			'company_id'      => 'Company ID',
			'contact_id'      => 'Contact ID',
			'consultant_id'   => 'Consultant ID',
			'preview_id'      => 'Preview ID',
			'category'        => 'Category',
			'availability'    => 'Availability',
			'delivery'        => 'Delivery',
			'deliveryPrice'   => 'Delivery Price',
			'price'           => 'Price',
			'benefit'         => 'Benefit',
			'tax'             => 'Tax',
			'count'           => 'Count',
			'state'           => 'State',
			'status'          => 'Status',
			'passive_type'    => 'Passive Type',
			'passive_comment' => 'Passive Comment',
			'archived_at'     => 'Archived At',
			'created_by_type' => 'Created By Type',
			'created_by_id'   => 'Created By ID',
			'created_at'      => 'Created At',
			'updated_at'      => 'Updated At',
			'deleted_at'      => 'Deleted At',
		];
	}

	public static function getCategories(): array
	{
		return [
			self::CATEGORY_RACKING,
			self::CATEGORY_MACHINE_TOOLS,
			self::CATEGORY_CRANE,
			self::CATEGORY_LIFTING,
			self::CATEGORY_LOADING_AND_UNLOADING,
			self::CATEGORY_SERVER,
			self::CATEGORY_OTHER,
		];
	}

	public static function getAvailabilities(): array
	{
		return [
			self::AVAILABILITY_STOCK,
			self::AVAILABILITY_ORDER,
		];
	}

	public static function getDeliveries(): array
	{
		return [
			self::DELIVERY_PAY,
			self::DELIVERY_IN_PRICE,
			self::DELIVERY_PICKUP,
		];
	}

	public static function getBenefits(): array
	{
		return [
			self::BENEFIT_YES,
			self::BENEFIT_STANDARD,
		];
	}

	public static function getTaxes(): array
	{
		return [
			self::TAX_VAT,
			self::TAX_NO,
		];
	}

	public static function getStates(): array
	{
		return [
			self::STATE_NEW,
			self::STATE_USED,
			self::STATE_BROKEN,
		];
	}

	public static function getStatuses(): array
	{
		return [
			self::STATUS_ACTIVE,
			self::STATUS_PASSIVE,
		];
	}

	public static function getPassiveTypes(): array
	{
		return [
			self::PASSIVE_TYPE_SOLD,
			self::PASSIVE_TYPE_OUTDATED,
			self::PASSIVE_TYPE_CANCELED,
			self::PASSIVE_TYPE_MODERATOR,
			self::PASSIVE_TYPE_OTHER,
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getCompany(): ActiveQuery
	{
		return $this->hasOne(Company::className(), ['id' => 'company_id']);
	}

	/**
	 * @return ActiveQuery|UserQuery
	 */
	public function getConsultant(): UserQuery
	{
		return $this->hasOne(User::className(), ['id' => 'consultant_id']);
	}

	/**
	 * @return ActiveQuery|ContactQuery
	 */
	public function getContact(): ContactQuery
	{
		return $this->hasOne(Contact::className(), ['id' => 'contact_id']);
	}

	/**
	 * @return ActiveQuery|MediaQuery
	 */
	public function getPreview(): MediaQuery
	{
		return $this->hasOne(Media::className(), ['id' => 'preview_id']);
	}

	/**
	 * @return RelationQuery|ActiveQuery
	 * @throws ErrorException
	 */
	public function getRelationFirst(): RelationQuery
	{
		return $this->morphHasMany(Relation::class, 'id', 'first');
	}

	/**
	 * @return ActiveQuery|MediaQuery
	 * @throws ErrorException
	 */
	public function getFiles(): MediaQuery
	{
		return $this->morphHasManyVia(Media::class, 'id', 'second')
		            ->via('relationFirst')
		            ->andWhere([Media::field('category') => 'equipment_file']);
	}

	/**
	 * @return ActiveQuery|MediaQuery
	 * @throws ErrorException
	 */
	public function getPhotos(): MediaQuery
	{
		return $this->morphHasManyVia(Media::class, 'id', 'second')
		            ->via('relationFirst')
		            ->andWhere([Media::field('category') => 'equipment_photo']);
	}

	public static function find(): EquipmentQuery
	{
		return new EquipmentQuery(get_called_class());
	}
}
