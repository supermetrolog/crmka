<?php

declare(strict_types=1);

namespace app\dto\Call;

use app\models\Call;
use app\models\Contact;
use app\models\User;
use yii\base\BaseObject;

class CreateCallDto extends BaseObject
{
	public User    $user;
	public Contact $contact;
	public int     $type   = Call::TYPE_OUTGOING;
	public int     $status = Call::STATUS_COMPLETED;
}