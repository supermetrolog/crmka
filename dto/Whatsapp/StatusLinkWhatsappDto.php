<?php

declare(strict_types=1);

namespace app\dto\Whatsapp;

use yii\base\BaseObject;

class StatusLinkWhatsappDto extends BaseObject
{
	public bool    $linked;
	public ?string $firstName;
	public ?string $fullName;
	public ?string $pushName;
	public ?string $phone;
}