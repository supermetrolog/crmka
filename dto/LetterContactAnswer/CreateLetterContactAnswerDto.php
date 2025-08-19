<?php

namespace app\dto\LetterContactAnswer;

use app\models\letter\LetterContact;
use app\models\User;
use yii\base\BaseObject;

class CreateLetterContactAnswerDto extends BaseObject
{
	public LetterContact $letterContact;
	public User          $markedBy;
	public string        $type;
	public ?string       $comment;
	public ?string       $related_message_id = null;
}
