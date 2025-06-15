<?php

namespace app\dto\Contact;

use yii\base\BaseObject;

class DisableContactDto extends BaseObject
{
	public int     $passive_why;
	public ?string $passive_why_comment = null;
}