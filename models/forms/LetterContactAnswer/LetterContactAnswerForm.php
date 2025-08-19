<?php

namespace app\models\forms\LetterContactAnswer;

use app\dto\LetterContactAnswer\CreateLetterContactAnswerDto;
use app\enum\Letter\LetterContactAnswerTypeEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\Form\Form;
use app\models\letter\LetterContact;
use app\models\User;

class LetterContactAnswerForm extends Form
{
	public int     $letter_contact_id;
	public int     $marked_by_id;
	public string  $type;
	public ?string $comment;
	public ?string $related_message_id = null;

	public function rules(): array
	{
		return [
			[['letter_contact_id', 'marked_by_id', 'type'], 'required'],
			[['related_message_id'], 'string'],
			[['type'], 'string', 'max' => 16],
			[['type'], EnumValidator::class, 'enumClass' => LetterContactAnswerTypeEnum::class],
			[['comment'], 'string', 'max' => 512],
			[['letter_contact_id', 'marked_by_id'], 'integer'],
			[['letter_contact_id'], 'exist', 'targetClass' => LetterContact::class, 'targetAttribute' => 'id'],
			[['marked_by_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
		];
	}

	public function getDto(): CreateLetterContactAnswerDto
	{
		return new CreateLetterContactAnswerDto([
			'type'               => $this->type,
			'comment'            => $this->comment,
			'related_message_id' => $this->related_message_id,
			'letterContact'      => LetterContact::findOne($this->letter_contact_id),
			'markedBy'           => User::findOne($this->marked_by_id),
		]);
	}
}
