<?php

namespace app\models;

use app\helpers\ArrayHelper;
use app\helpers\NumberHelper;
use app\helpers\StringHelper;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ActiveQuery\CompanyQuery;
use app\models\ActiveQuery\ContactQuery;
use app\models\ActiveQuery\DealQuery;
use app\models\ActiveQuery\FolderEntityQuery;
use app\models\ActiveQuery\RequestQuery;
use app\models\ActiveQuery\TaskQuery;
use app\models\ActiveQuery\TaskRelationEntityQuery;
use app\models\ActiveQuery\TimelineQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\miniModels\RequestDirection;
use app\models\miniModels\RequestDistrict;
use app\models\miniModels\RequestGateType;
use app\models\miniModels\RequestObjectClass;
use app\models\miniModels\RequestObjectType;
use app\models\miniModels\RequestObjectTypeGeneral;
use app\models\miniModels\RequestRegion;
use Yii;
use yii\base\ErrorException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "request".
 *
 * @property int                        $id
 * @property int                        $company_id                    [связь] ID компании
 * @property string                     $name                          Название
 * @property int                        $dealType                      Тип сделки
 * @property int|null                   $expressRequest                [флаг] Срочный запрос
 * @property int|null                   $distanceFromMKAD              Удаленность от МКАД
 * @property int|null                   $distanceFromMKADnotApplicable [флаг] Неприменимо
 * @property int                        $minArea                       Минимальная площадь пола
 * @property int                        $maxArea                       Максимальная площадь пола
 * @property int                        $minCeilingHeight              Минимальная высота потолков
 * @property int                        $maxCeilingHeight              максимальная высота потолков
 * @property int|null                   $firstFloorOnly                [флаг] Только 1 этаж
 * @property int                        $heated                        [флаг] Отапливаемый
 * @property int|null                   $antiDustOnly                  [флаг] Только антипыль
 * @property int|null                   $trainLine                     [флаг] Ж/Д ветка
 * @property int|null                   $trainLineLength               Длина Ж/Д
 * @property int                        $consultant_id                 [связь] ID консультанта
 * @property string|null                $description                   Описание
 * @property int|null                   $pricePerFloor                 Цена за пол
 * @property int|null                   $electricity                   Электричество
 * @property int|null                   $haveCranes                    [флаг] Наличие кранов
 * @property int|null                   $status                        [флаг] Статус
 * @property int|null                   $passive_why
 * @property string|null                $passive_why_comment
 * @property string|null                $created_at
 * @property string|null                $updated_at
 * @property string|null                $movingDate                    Дата переезда
 * @property int|null                   $unknownMovingDate             [флаг] Нет конкретики по сроку переезда/рассматривает постоянно
 * @property int|null                   $water                         [флаг]
 * @property int|null                   $sewerage                      [флаг]
 * @property int|null                   $gaz                           [флаг]
 * @property int|null                   $steam                         [флаг]
 * @property int|null                   $shelving                      [флаг]
 * @property int|null                   $outside_mkad                  [флаг] Вне мкад (если выбран регоин МОСКВА)
 * @property int|null                   $region_neardy                 [флаг] Регионы рядом
 * @property int|null                   $contact_id                    [связь] с контактом
 * @property string|null                $related_updated_at            дата последнего обновления связанных с запросом сущностей
 *
 * @property Company                    $company
 * @property User                       $consultant
 * @property RequestDirection[]         $directions
 * @property RequestDistrict[]          $districts
 * @property RequestGateType[]          $gateTypes
 * @property RequestObjectClass[]       $objectClasses
 * @property RequestObjectType[]        $objectTypes
 * @property RequestObjectTypeGeneral[] $objectTypesGeneral
 * @property RequestRegion[]            $regions
 * @property Timeline[]                 $timelines
 * @property-read Timeline[]            $activeTimelines
 * @property-read Timeline              $mainTimeline
 * @property-read ?Contact              $contact
 */
class Request extends AR
{
	public const STATUS_ACTIVE              = 1;
	public const STATUS_PASSIVE             = 0;
	public const STATUS_DONE                = 2;
	public const DEAL_TYPE_LIST             = ['Аренда', 'Продажа', 'Ответ-хранение', 'Субаренда'];
	public const DEAL_TYPE_RENT             = 0;
	public const DEAL_TYPE_SALE             = 1;
	public const DEAL_TYPE_RESPONSE_STORAGE = 2;
	public const DEAL_TYPE_SUBLEASE         = 3;

	public const PASSIVE_WHY_BLOCK        = 0;
	public const PASSIVE_WHY_ALREADY_RENT = 1;
	public const PASSIVE_WHY_ALREADY_BUY  = 2;
	public const PASSIVE_WHY_OUTDATED     = 3;
	public const PASSIVE_WHY_SUSPEND      = 4;
	public const PASSIVE_WHY_OTHER        = 5;
	public const PASSIVE_WHY_SURVEY       = 6;

	public const UNKNOWN_MOVING_DATE_REASON_CONSTANTLY  = 0;
	public const UNKNOWN_MOVING_DATE_REASON_NO_DEADLINE = 1;

	public const REQUEST_CREATED_EVENT = 'request_created_event';
	public const REQUEST_UPDATED_EVENT = 'request_updated_event';

	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;

	public static function getStatuses(): array
	{
		return [
			self::STATUS_PASSIVE,
			self::STATUS_ACTIVE,
			self::STATUS_DONE,
		];
	}

	public static function getPassiveWhyReasons(): array
	{
		return [
			self::PASSIVE_WHY_BLOCK,
			self::PASSIVE_WHY_ALREADY_RENT,
			self::PASSIVE_WHY_ALREADY_BUY,
			self::PASSIVE_WHY_OUTDATED,
			self::PASSIVE_WHY_SUSPEND,
			self::PASSIVE_WHY_OTHER,
			self::PASSIVE_WHY_SURVEY,
		];
	}

	public static function getDealTypes(): array
	{
		return [
			self::DEAL_TYPE_RENT,
			self::DEAL_TYPE_SALE,
			self::DEAL_TYPE_RESPONSE_STORAGE,
			self::DEAL_TYPE_SUBLEASE,
		];
	}

	public static function getUnknownMovingDateReasons(): array
	{
		return [
			self::UNKNOWN_MOVING_DATE_REASON_CONSTANTLY,
			self::UNKNOWN_MOVING_DATE_REASON_NO_DEADLINE,
		];
	}

	public function init(): void
	{
		$this->on(self::REQUEST_CREATED_EVENT, [Yii::$app->notify, 'notifyUser']);
		parent::init();
	}

	public static function tableName(): string
	{
		return 'request';
	}

	public function rules(): array
	{
		return [
			[['name', 'passive_why_comment'], 'string', 'max' => 255],
			[['description'], 'string'],
			[['company_id', 'consultant_id', 'dealType', 'minCeilingHeight', 'minArea', 'maxArea'], 'required'],
			[
				[
					'outside_mkad', 'region_neardy', 'distanceFromMKADnotApplicable',
					'firstFloorOnly', 'antiDustOnly', 'expressRequest',
					'heated', 'water', 'sewerage', 'gaz', 'steam', 'shelving', 'trainLine', 'haveCranes',
				],
				'boolean'
			],
			[
				[
					'contact_id', 'company_id', 'consultant_id',
					'dealType', 'status', 'pricePerFloor', 'passive_why', 'unknownMovingDate', 'electricity',
					'distanceFromMKAD',
					'minArea', 'maxArea', 'minCeilingHeight', 'maxCeilingHeight', 'trainLineLength'
				], 'integer'
			],
			[['related_updated_at', 'created_at', 'updated_at', 'movingDate'], 'safe'],
			[['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
			[['consultant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['consultant_id' => 'id']],
			[['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::class, 'targetAttribute' => ['contact_id' => 'id']],
		];
	}

	public function getFormatName(): string
	{
		$name = StringHelper::join(' - ', $this->name ?? "", self::DEAL_TYPE_LIST[$this->dealType]);
		$area = StringHelper::join(' - ', $this->minArea, $this->maxArea);

		return StringHelper::join(' ', $name, $area, 'м');
	}

	public function fields(): array
	{
		$fields = parent::fields();

		$fields['format_name'] = fn() => $this->getFormatName();

		$fields['pricePerFloorMonth'] = fn() => $this->getPricePerFloorMonth();

		return $fields;
	}

	public function getPricePerFloorMonth(): float
	{
		if (is_null($this->pricePerFloor) || $this->pricePerFloor === 0) {
			return 0;
		}

		return NumberHelper::round($this->pricePerFloor / 12, 2);
	}

	public function getTimelineProgress(): ?float
	{
		$mainTimeline = $this->mainTimeline;

		if (!$mainTimeline) {
			return null;
		}

		$doneTimelineStepCount = ArrayHelper::length($mainTimeline->doneTimelineSteps);

		return NumberHelper::calculatePercentage($doneTimelineStepCount, Timeline::MAX_STEP_COUNT);
	}

	public function extraFields()
	{
		$extraFields = parent::extraFields();

		$extraFields['timeline_progress'] = fn() => $this->getTimelineProgress();

		return $extraFields;
	}

	public function getCompany(): CompanyQuery
	{
		/** @var CompanyQuery */
		return $this->hasOne(Company::class, ['id' => 'company_id']);
	}

	public function getConsultant(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'consultant_id']);
	}

	public function getDeal(): DealQuery
	{
		/** @var DealQuery */
		return $this->hasOne(Deal::class, ['request_id' => 'id']);
	}

	public function getContact(): ContactQuery
	{
		/** @var ContactQuery */
		return $this->hasOne(Contact::class, ['id' => 'contact_id']);
	}

	public function getDirections(): ActiveQuery
	{
		return $this->hasMany(RequestDirection::class, ['request_id' => 'id']);
	}

	public function getDistricts(): ActiveQuery
	{
		return $this->hasMany(RequestDistrict::class, ['request_id' => 'id']);
	}

	public function getGateTypes(): ActiveQuery
	{
		return $this->hasMany(RequestGateType::class, ['request_id' => 'id']);
	}

	public function getObjectClasses(): ActiveQuery
	{
		return $this->hasMany(RequestObjectClass::class, ['request_id' => 'id']);
	}

	public function getObjectTypes(): ActiveQuery
	{
		return $this->hasMany(RequestObjectType::class, ['request_id' => 'id']);
	}

	public function getObjectTypesGeneral(): ActiveQuery
	{
		return $this->hasMany(RequestObjectTypeGeneral::class, ['request_id' => 'id']);
	}

	public function getRegions(): ActiveQuery
	{
		return $this->hasMany(RequestRegion::class, ['request_id' => 'id']);
	}

	public function getTimelines(): TimelineQuery
	{
		/** @var TimelineQuery */
		return $this->hasMany(Timeline::class, ['request_id' => 'id']);
	}

	/**
	 * @throws ErrorException
	 */
	public function getActiveTimelines(): TimelineQuery
	{
		/** @var TimelineQuery */
		return $this->hasMany(Timeline::class, [
			'request_id' => 'id'
		])->andOnCondition([Timeline::field('status') => Timeline::STATUS_ACTIVE]);
	}

	public function getMainTimeline(): TimelineQuery
	{
		/** @var TimelineQuery */
		return $this->hasOne(Timeline::class, ['request_id' => 'id', 'consultant_id' => 'consultant_id']);
	}

	/**
	 * @throws ErrorException
	 */
	public function getChatMember(): ChatMemberQuery
	{
		/** @var ChatMemberQuery */
		return $this->morphHasOne(ChatMember::class);
	}

	/**
	 * @throws ErrorException
	 */
	public function getTaskRelationEntities(): TaskRelationEntityQuery
	{
		/** @var TaskRelationEntityQuery */
		return $this->hasMany(TaskRelationEntity::class, ['entity_id' => 'id'])->andOnCondition([
			TaskRelationEntity::field('entity_type') => self::getMorphClass(),
			TaskRelationEntity::field('deleted_at')  => null
		]);
	}

	/**
	 * @throws ErrorException
	 */
	public function getTasks(): TaskQuery
	{
		/** @var TaskQuery */
		return $this->hasMany(Task::class, ['id' => 'task_id'])
		            ->via('taskRelationEntities')
		            ->andOnCondition([Task::field('deleted_at') => null]);
	}

	/**
	 * @throws ErrorException
	 */
	public function getFolderEntities(): FolderEntityQuery
	{
		/** @var FolderEntityQuery */
		return $this->hasMany(FolderEntity::class, ['entity_id' => 'id'])->andOnCondition([FolderEntity::field('entity_type') => self::getMorphClass()]);
	}

	public static function find(): RequestQuery
	{
		return new RequestQuery(static::class);
	}

	public function isActive(): bool
	{
		return $this->status === self::STATUS_ACTIVE;
	}

	public function isPassive(): bool
	{
		return $this->status === self::STATUS_PASSIVE;
	}

	public function isCompleted(): bool
	{
		return $this->status === self::STATUS_DONE;
	}
}
