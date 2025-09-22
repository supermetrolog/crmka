<?php

declare(strict_types=1);

namespace app\dto\Call;

use app\models\Call;
use app\models\Contact;
use app\models\miniModels\Phone;
use app\models\User\User;
use yii\base\BaseObject;

class CreateCallDto extends BaseObject
{
	public User    $user;
	public Contact $contact;
	public ?Phone  $phone  = null;
	public int     $type   = Call::TYPE_OUTGOING;
	public int     $status = Call::STATUS_COMPLETED;
	public ?string $description;
}