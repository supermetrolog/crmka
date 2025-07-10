<?php

declare(strict_types=1);

namespace app\dto\Call;

use app\models\Contact;
use app\models\miniModels\Phone;
use yii\base\BaseObject;

class UpdateCallDto extends BaseObject
{
	public int     $type;
	public int     $status;
	public Contact $contact;
	public ?Phone  $phone = null;
	public ?string $description;
}