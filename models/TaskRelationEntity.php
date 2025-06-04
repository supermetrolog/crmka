<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\CompanyQuery;
use app\models\ActiveQuery\ContactQuery;
use app\models\ActiveQuery\OfferMixQuery;
use app\models\ActiveQuery\RequestQuery;
use app\models\ActiveQuery\SurveyQuery;
use app\models\ActiveQuery\TaskQuery;
use app\models\ActiveQuery\TaskRelationEntityQuery;
use app\models\ActiveQuery\UserQuery;
use InvalidArgumentException;
use yii\db\ActiveQuery;

/**
 * @property int                               $id
 * @property int                               $task_id
 * @property int                               $entity_id
 * @property string                            $entity_type
 * @property ?int                              $created_by_id
 * @property ?int                              $deleted_by_id
 * @property ?string                           $comment
 * @property string                            $relation_type
 * @property string                            $created_at
 * @property ?string                           $updated_at
 * @property ?string                           $deleted_at
 *
 * @property-read ?User                        $createdBy
 * @property-read ?User                        $deletedBy
 * @property-read Task                         $task
 * @property-read ?Task                        $relatedTask
 * @property-read ?Contact                     $contact
 * @property-read ?Company                     $company
 * @property-read ?Request                     $request
 * @property-read Task|Contact|Company|Request $entity
 * @property-read OfferMix                     $offerMix
 * @property-read Objects                      $object
 * @property-read Survey                       $survey
 */
class TaskRelationEntity extends AR
{
	public const RELATION_TYPE_MANUAL = 'manual';
	public const RELATION_TYPE_SYSTEM = 'system';

	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;

	public static function tableName(): string
	{
		return 'task_relation_entity';
	}

	public static function getAvailableEntityTypes(): array
	{
		return [
			Company::getMorphClass(),
			Task::getMorphClass(),
			Request::getMorphClass(),
			Contact::getMorphClass(),
			Survey::getMorphClass(),
			OfferMix::getMorphClass(),
			Objects::getMorphClass()
		];

		// TODO: OfferMix, Object, Equipment, Call, Survey etc
	}

	public static function getEntityMorphMap(): array
	{
		return [
			Company::getMorphClass()  => Company::class,
			Task::getMorphClass()     => Task::class,
			Request::getMorphClass()  => Request::class,
			Contact::getMorphClass()  => Contact::class,
			Survey::getMorphClass()   => Survey::class,
			OfferMix::getMorphClass() => OfferMix::class,
			Objects::getMorphClass()  => Objects::class
		];
	}

	public function rules(): array
	{
		return [
			[['task_id', 'relation_type', 'entity_id', 'entity_type'], 'required'],
			[['task_id', 'created_by_id', 'deleted_by_id', 'entity_id'], 'integer'],
			['relation_type', 'string', 'max' => 16],
			['comment', 'string', 'max' => 255],
			['entity_type', 'string'],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
			['task_id', 'exist', 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
			['created_by_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => ['created_by_id' => 'id']],
			['deleted_by_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => ['deleted_by_id' => 'id']],
		];
	}

	public function getTask(): TaskQuery
	{
		/** @var TaskQuery */
		return $this->hasOne(Task::class, ['id' => 'task_id']);
	}

	public function getCreatedBy(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'created_by_id']);
	}

	public function getDeletedBy(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'deleted_by_id']);
	}

	public function morphBelongTo($class, string $column = 'id', string $morphColumn = 'entity', string $ownerColumn = 'morph'): ActiveQuery
	{
		return parent::morphBelongTo($class, $column, $morphColumn);
	}

	public function getCompany(): CompanyQuery
	{
		/** @var CompanyQuery */
		return $this->morphBelongTo(Company::class);
	}

	public function getRequest(): RequestQuery
	{
		/** @var RequestQuery */
		return $this->morphBelongTo(Request::class);
	}

	public function getContact(): ContactQuery
	{
		/** @var ContactQuery */
		return $this->morphBelongTo(Contact::class);
	}

	public function getRelatedTask(): TaskQuery
	{
		/** @var TaskQuery */
		return $this->morphBelongTo(Task::class);
	}

	public function getOfferMix(): OfferMixQuery
	{
		/** @var OfferMixQuery */
		return $this->morphBelongTo(OfferMix::class);
	}

	public function getObject(): ActiveQuery
	{
		/** @var ActiveQuery */
		return $this->morphBelongTo(Objects::class);
	}

	public function getSurvey(): SurveyQuery
	{
		/** @var SurveyQuery */
		return $this->morphBelongTo(Survey::class);
	}

	/** @return Task|Contact|Company|Request|OfferMix|Objects|Survey */
	public function getEntity()
	{
		switch ($this->entity_type) {
			case Company::getMorphClass():
				return $this->company;
			case Request::getMorphClass():
				return $this->request;
			case Contact::getMorphClass():
				return $this->contact;
			case Task::getMorphClass():
				return $this->relatedTask;
			case OfferMix::getMorphClass():
				return $this->offerMix;
			case Objects::getMorphClass():
				return $this->object;
			case Survey::getMorphClass():
				return $this->survey;
			default:
				throw new InvalidArgumentException("Unexpected TaskRelationEntity type: " . $this->entity_type);
		}
	}

	public static function find(): TaskRelationEntityQuery
	{
		return new TaskRelationEntityQuery(static::class);
	}

	public function isSystemRelation(): bool
	{
		return $this->relation_type === self::RELATION_TYPE_SYSTEM;
	}

	public function isManualRelation(): bool
	{
		return $this->relation_type === self::RELATION_TYPE_MANUAL;
	}
}
