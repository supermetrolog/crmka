<?php

declare(strict_types=1);

namespace app\dto\Call;

use app\models\Contact;
use yii\base\BaseObject;

class UpdateCallDto extends BaseObject
{
	public int     $type;
	public int     $status;
	public Contact $contact;
}