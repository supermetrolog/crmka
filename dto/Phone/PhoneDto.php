<?php

declare(strict_types=1);

namespace app\dto\Phone;

use app\models\Contact;
use yii\base\BaseObject;

class PhoneDto extends BaseObject
{
	public ?Contact $contact = null;

	public string  $phone;
	public ?string $exten;
	public ?int    $isMain;
	public string  $type;
	public string  $country_code;
	public ?string $comment;

}