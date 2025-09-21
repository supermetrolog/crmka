<?php

declare(strict_types=1);

namespace app\components\Telegram\Models;

class TContact extends TModel
{
	public string  $phone_number;
	public string  $first_name;
	public ?string $last_name = null;
	public ?int    $user_id   = null;
}
