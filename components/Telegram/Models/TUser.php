<?php

declare(strict_types=1);

namespace app\components\Telegram\Models;

class TUser extends TModel
{
	public int     $id;
	public string  $first_name;
	public ?string $last_name = null;
	public ?string $username  = null;
}
