<?php

namespace app\models;

use app\enum\Letter\LetterContactAnswerTypeEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\UserQuery;
use app\models\letter\Letter;
use app\models\letter\LetterContact;
use yii\db\ActiveQuery;

/**
 *
 * @property int                $id
 * @property int                $letter_contact_id
 * @property int                $marked_by_id
 * @property string             $marked_at
 * @property string             $type
 * @property ?string            $comment
 * @property ?string            $related_message_id
 * @property ?string            $deleted_at
 *
 * @property-read LetterContact $letterContact
 * @property-read Letter        $letter
 * @property-read User          $markedBy
 */
class LetterContactAnswer extends AR
{
	protected bool $useSoftDelete = true;

	public static function tableName(): string
	{
		return 'letter_contact_answer';
	}

	public function rules(): array
	{
		return [
			[['letter_contact_id', 'marked_by_id', 'marked_at', 'type'], 'required'],
			[['deleted_at', 'related_message_id', 'marked_at'], 'safe'],
			[['type'], 'string', 'max' => 16],
			[['type'], EnumValidator::class, 'enumClass' => LetterContactAnswerTypeEnum::class],
			[['comment'], 'string', 'max' => 512],
			[['letter_contact_id', 'marked_by_id'], 'integer'],
			[['letter_contact_id'], 'exist', 'targetClass' => LetterContact::class, 'targetAttribute' => ['letter_contact_id' => 'id']],
			[['marked_by_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['marked_by_id' => 'id']],
		];
	}

	public function getLetterContact(): ActiveQuery
	{
		return $this->hasOne(LetterContact::class, ['id' => 'letter_contact_id']);
	}

	public function getLetter(): ActiveQuery
	{
		return $this->hasOne(Letter::class, ['id' => 'letter_id'])->via('letterContact');
	}

	public function getMarkedBy(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'marked_by_id']);
	}

	public static function find(): AQ
	{
		return (new AQ(static::class))->andWhereNull('deleted_at');
	}
}
