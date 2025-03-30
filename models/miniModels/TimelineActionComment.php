<?php

namespace app\models\miniModels;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\TimelineQuery;
use app\models\letter\Letter;
use app\models\Timeline;
use yii\db\ActiveQuery;

// TODO: Добавить created_by_id для привязки сотрудников

/**
 * This is the model class for table "timeline_action_comment".
 *
 * @property int               $id
 * @property int               $timeline_step_id     [связь]
 * @property int               $timeline_id          [связь]
 * @property int               $timeline_step_number номер степа
 * @property int               $type                 тип коммента
 * @property int               $letter_id            [связь] с письмом (letter)
 * @property string            $comment              комментарий к действию
 * @property string            $created_at
 * @property ?string           $title
 *
 * @property-read TimelineStep $timelineStep
 * @property-read Timeline     $timeline
 * @property-read ?Letter      $letter
 */
class TimelineActionComment extends AR
{
	public const SYSTEM_COMMENT_TITLE = 'система';

	public const TYPE_DEFAULT             = 1;
	public const TYPE_NOTIFICATION        = 2;
	public const TYPE_ALREADY_SEND_OFFERS = 3;
	public const TYPE_SEND_OFFERS         = 4;
	public const TYPE_DONE                = 5;

	public static function tableName(): string
	{
		return 'timeline_action_comment';
	}

	public function rules(): array
	{
		return [
			[['timeline_step_id', 'comment'], 'required'],
			[['timeline_step_id', 'timeline_id', 'timeline_step_number', 'type', "letter_id"], 'integer'],
			[['created_at'], 'safe'],
			[['comment', 'title'], 'string'],
			[['timeline_step_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimelineStep::class, 'targetAttribute' => ['timeline_step_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'               => 'ID',
			'timeline_step_id' => 'Timeline Step ID',
			'comment'          => 'Comment',
			'created_at'       => 'Created At',
			'title'            => 'Title',
			'type'             => 'Type',
			'letter_id'        => 'Letter ID',
		];
	}

	public function getTimelineStep(): ActiveQuery
	{
		return $this->hasOne(TimelineStep::class, ['id' => 'timeline_step_id']);
	}

	public function getTimeline(): TimelineQuery
	{
		/** @var TimelineQuery */
		return $this->hasOne(Timeline::class, ['id' => 'timeline_id']);
	}

	public function getLetter(): ActiveQuery
	{
		return $this->hasOne(Letter::class, ['id' => 'letter_id']);
	}
}
