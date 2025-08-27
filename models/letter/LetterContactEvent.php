<?php

namespace app\models\letter;

use app\enum\Letter\LetterContactEventTypeEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\traits\EnumAttributeLabelTrait;
use yii\db\ActiveQuery;

/**
 * @property int                $id
 * @property int                $letter_contact_id
 * @property string             $event_type
 * @property string             $ip
 * @property string             $user_agent
 * @property string             $created_at
 *
 * @property-read LetterContact $letterContact
 */
class LetterContactEvent extends AR
{
	use EnumAttributeLabelTrait;

	public static function tableName(): string
	{
		return 'letter_contact_event';
	}

	public function rules(): array
	{
		return [
			[['letter_contact_id'], 'required'],
			[['letter_contact_id'], 'integer'],
			[['event_type', 'ip', 'user_agent'], 'string'],
			['event_type', EnumValidator::class, 'enumClass' => LetterContactEventTypeEnum::class],
			[['created_at'], 'string'],
			[['letter_contact_id'], 'exist', 'targetClass' => LetterContact::class, 'targetAttribute' => 'id'],
		];
	}

	public static function find(): AQ
	{
		return new AQ(static::class);
	}

	public function getLetterContact(): ActiveQuery
	{
		return $this->hasOne(LetterContact::class, ['id' => 'letter_contact_id']);
	}

	public function getEventTypeLabel(): string
	{
		return $this->getEnumLabel('event_type', LetterContactEventTypeEnum::class);
	}
}
