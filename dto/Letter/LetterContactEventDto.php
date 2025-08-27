<?php

namespace app\dto\Letter;

use app\models\letter\LetterContact;
use yii\base\BaseObject;

class LetterContactEventDto extends BaseObject
{
	public LetterContact $letterContact;
	public string        $eventType;
	public string        $ip;
	public string        $userAgent;
}
