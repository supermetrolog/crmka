<?php

declare(strict_types=1);

namespace app\dto\User;

use yii\base\BaseObject;

class UserWhatsappLinkDto extends BaseObject
{
	public int     $userId;
	public string  $whatsappProfileId;
	public string  $phone;
	public ?string $firstName;
	public ?string $fullName;
	public ?string $pushName;
}