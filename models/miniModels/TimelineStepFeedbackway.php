<?php

namespace app\models\miniModels;

use app\kernel\common\models\AR\AR;
use yii\db\ActiveQuery;

/**
 *
 * @property int                $id
 * @property int                $timeline_step_id [связь] с конкретным шагом таймлайна
 * @property ?int               $way              Способ получения обратной связи
 * @property string|null        $created_at
 * @property string|null        $updated_at
 *
 * @property-read ?TimelineStep $timelineStep
 */
class TimelineStepFeedbackway extends AR
{
	public const MAIN_COLUMN = 'way';

	public const WAY_EMAIL         = 0;
	public const WAY_CALL_OUTGOING = 1;
	public const WAY_CALL_INCOMING = 2;
	public const WAY_CALL_WHATSAPP = 3;
	public const WAY_CALL_VIBER    = 4;
	public const WAY_CALL_SMS      = 5;
	public const WAY_CALL_TELEGRAM = 6;

	public static function getWays(): array
	{
		return [
			self::WAY_EMAIL,
			self::WAY_CALL_OUTGOING,
			self::WAY_CALL_INCOMING,
			self::WAY_CALL_WHATSAPP,
			self::WAY_CALL_VIBER,
			self::WAY_CALL_SMS,
			self::WAY_CALL_TELEGRAM
		];
	}

	public static function tableName(): string
	{
		return 'timeline_step_feedbackway';
	}

	public function rules(): array
	{
		return [
			[['timeline_step_id'], 'required'],
			[['timeline_step_id', 'way'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['timeline_step_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimelineStep::className(), 'targetAttribute' => ['timeline_step_id' => 'id']],
		];
	}

	public function getTimelineStep(): ActiveQuery
	{
		return $this->hasOne(TimelineStep::class, ['id' => 'timeline_step_id']);
	}
}
