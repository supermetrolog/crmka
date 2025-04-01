<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\RequestQuery;
use app\models\ActiveQuery\TimelineQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\miniModels\TimelineActionComment;
use app\models\miniModels\TimelineStep;
use yii\base\ErrorException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "timeline".
 *
 * @property int                          $id
 * @property int                          $request_id    [связь] с запросами
 * @property int                          $consultant_id [связь] с юзерами
 * @property ?int                         $status
 * @property ?string                      $created_at
 * @property ?string                      $updated_at
 *
 * @property-read User                    $consultant
 * @property-read Request                 $request
 * @property-read TimelineStep[]          $timelineSteps
 * @property-read TimelineStep[]          $doneTimelineSteps
 * @property-read TimelineActionComment[] $timelineActionComments
 */
class Timeline extends AR
{
	public const STATUS_ACTIVE                = 1;
	public const STATUS_INACTIVE              = 0;
	public const STATUS_INACTIVE_WHEN_TIMEOUT = -1;

	public const MAX_STEP_COUNT = 8;

	public static function getStatuses(): array
	{
		return [
			self::STATUS_INACTIVE_WHEN_TIMEOUT,
			self::STATUS_INACTIVE,
			self::STATUS_ACTIVE,
		];
	}

	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;

	public static function tableName(): string
	{
		return 'timeline';
	}

	public function rules(): array
	{
		return [
			[['request_id', 'consultant_id'], 'required'],
			[['request_id', 'consultant_id', 'status'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['consultant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['consultant_id' => 'id']],
			[['request_id'], 'exist', 'skipOnError' => true, 'targetClass' => Request::class, 'targetAttribute' => ['request_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'            => 'ID',
			'request_id'    => 'Request ID',
			'consultant_id' => 'Consultant ID',
			'status'        => 'Status',
			'created_at'    => 'Created At',
			'updated_at'    => 'Updated At',
		];
	}

	public function getConsultant(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'consultant_id']);
	}

	public function getRequest(): RequestQuery
	{
		/** @var RequestQuery */
		return $this->hasOne(Request::class, ['id' => 'request_id']);
	}

	public function getTimelineSteps(): ActiveQuery
	{
		return $this->hasMany(TimelineStep::class, ['timeline_id' => 'id']);
	}

	/**
	 * @throws ErrorException
	 */
	public function getDoneTimelineSteps(): ActiveQuery
	{
		return $this->getTimelineSteps()->onCondition([TimelineStep::field('status') => TimelineStep::STATUS_COMPLETED]);
	}

	/**
	 * @throws ErrorException
	 */
	public function getTimelineActionComments(): ActiveQuery
	{
		return $this->hasMany(TimelineActionComment::class, ['timeline_id' => 'id'])->orderBy([TimelineActionComment::field('created_at') => SORT_DESC]);
	}

	public static function find(): TimelineQuery
	{
		return new TimelineQuery(static::class);
	}

	public function isPassive(): bool
	{
		return $this->status !== self::STATUS_ACTIVE;
	}

	public function isActive(): bool
	{
		return $this->status === self::STATUS_ACTIVE;
	}
}
