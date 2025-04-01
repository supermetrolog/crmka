<?php

namespace app\models\miniModels;

use app\helpers\ArrayHelper;
use app\helpers\TypeConverterHelper;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\TimelineQuery;
use app\models\Timeline;
use yii\db\ActiveQuery;

/**
 * @property int                            $id
 * @property int                            $timeline_id [связь] с таймлайном
 * @property int                            $number      номер шага
 * @property ?string                        $comment     общий комментарий к шагу
 * @property ?int                           $done        [флаг] ГОТОВО - используется для любого шага
 * @property ?int                           $negative    [флаг] ОТРИЦАНИЕ - используется для любого шага
 * @property ?int                           $additional  [флаг] ДОПОЛНИТЕЛЬНЫЙ ФЛАГ - используется для любого шага
 * @property ?string                        $date        ДАТА используется для любого шага
 * @property ?int                           $status      [флаг]
 * @property ?string                        $created_at
 * @property ?string                        $updated_at
 *
 * @property-read Timeline                  $timeline
 * @property-read TimelineStepFeedbackway[] $timelineStepFeedbackways
 * @property-read TimelineStepObject[]      $timelineStepObjects
 * @property-read TimelineActionComment[]   $timelineActionComments
 */
class TimelineStep extends AR
{
	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;

	public const MEETING_STEP_NUMBER    = 0;
	public const OFFER_STEP_NUMBER      = 1;
	public const FEEDBACK_STEP_NUMBER   = 2;
	public const INSPECTION_STEP_NUMBER = 3;
	public const VISIT_STEP_NUMBER      = 4;
	public const INTEREST_STEP_NUMBER   = 5;
	public const TALK_STEP_NUMBER       = 6;
	public const DEAL_STEP_NUMBER       = 7;

	public const STATUS_COMPLETED = 1;
	public const STATUS_PROCESSED = 0;

	public static function getStatuses(): array
	{
		return [
			self::STATUS_COMPLETED,
			self::STATUS_PROCESSED,
		];
	}

	public const STEPS = [
		self::MEETING_STEP_NUMBER    => 'Знакомство',
		self::OFFER_STEP_NUMBER      => 'Предложения',
		self::FEEDBACK_STEP_NUMBER   => 'Обратная связь',
		self::INSPECTION_STEP_NUMBER => 'Организация осмотра',
		self::VISIT_STEP_NUMBER      => 'Осмотр',
		self::INTEREST_STEP_NUMBER   => 'Заинтересованность',
		self::TALK_STEP_NUMBER       => 'Переговоры',
		self::DEAL_STEP_NUMBER       => 'Сделка',
	];

	public const IS_NEGATIVE = 1;

	public static function tableName(): string
	{
		return 'timeline_step';
	}

	public function rules(): array
	{
		return [
			[['timeline_id', 'number'], 'required'],
			[['timeline_id', 'number', 'done', 'negative', 'additional', 'status'], 'integer'],
			[['date', 'created_at', 'updated_at'], 'safe'],
			[['comment'], 'string', 'max' => 255],
			[['timeline_id'], 'exist', 'skipOnError' => true, 'targetClass' => Timeline::class, 'targetAttribute' => ['timeline_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'          => 'ID',
			'timeline_id' => 'Timeline ID',
			'number'      => 'Number',
			'comment'     => 'Comment',
			'done'        => 'Done',
			'negative'    => 'Negative',
			'additional'  => 'Additional',
			'date'        => 'Date',
			'created_at'  => 'Created At',
			'updated_at'  => 'Updated At',
		];
	}

	public function extraFields()
	{
		$extraFields = parent::extraFields();

		// TODO: Удалить когда избавимся от всех expand в проекте
		$extraFields['timelineStepObjects'] = static function ($extraFields) {
			$count = array_count_values(ArrayHelper::column($extraFields['timelineStepObjects'], 'offer_id'));

			$objects = [];

			/** @var TimelineStepObject $value */
			foreach ($extraFields['timelineStepObjects'] as $value) {

				$object = $value->toArray([], [
					'comments',
					'offer.object',
					'offer.generalOffersMix.offer'
				]);

				if ($object['offer'] !== null) {
					$object['offer']['comments']        = $object['comments'];
					$object['offer']['duplicate_count'] = $count[$object['offer_id']];
				}

				$object['duplicate_count'] = $count[$object['offer_id']];

				$objects[$object['offer_id']] = $object;
			}

			return ArrayHelper::values($objects);
		};

		return $extraFields;
	}

	public function getUniqueObjects(): array
	{
		$countMap = ArrayHelper::reduce($this->timelineStepObjects, static function ($carry, TimelineStepObject $item) {
			$carry[$item->offer_id] = isset($carry[$item->offer_id]) ? $carry[$item->offer_id] + 1 : 1;

			return $carry;
		}, []);

		$objects = [];

		foreach ($this->timelineStepObjects as $value) {
			$objects[$value['offer_id']] = $value;
			$objects[$value['offer_id']]->setDuplicateCount($countMap[$value['offer_id']]);
		}

		return ArrayHelper::values($objects);
	}

	public static function find(): AQ
	{
		return new AQ(static::class);
	}

	public function getTimeline(): TimelineQuery
	{
		/** @var TimelineQuery */
		return $this->hasOne(Timeline::class, ['id' => 'timeline_id']);
	}

	public function getTimelineStepFeedbackways(): ActiveQuery
	{
		return $this->hasMany(TimelineStepFeedbackway::class, ['timeline_step_id' => 'id']);
	}

	public function getTimelineStepObjects(): ActiveQuery
	{
		return $this->hasMany(TimelineStepObject::class, ['timeline_step_id' => 'id']);
	}

	public function getTimelineActionComments(): ActiveQuery
	{
		return $this->hasMany(TimelineActionComment::class, ['timeline_step_id' => 'id']);
	}

	public function isDone(): bool
	{
		if (is_null($this->done)) {
			return false;
		}

		return TypeConverterHelper::toBool($this->done);
	}

	public function isNegative(): bool
	{
		if (is_null($this->negative)) {
			return false;
		}

		return TypeConverterHelper::toBool($this->negative);
	}

	public function isProcessed(): bool
	{
		return $this->status === self::STATUS_PROCESSED;
	}

	public function isCompleted(): bool
	{
		return $this->status === self::STATUS_COMPLETED;
	}
}
