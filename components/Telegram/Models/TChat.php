<?php

declare(strict_types=1);

namespace app\components\Telegram\Models;

class TChat extends TModel
{
	public int     $id;
	public string  $type;
	public ?string $title      = null;
	public ?string $username   = null;
	public ?string $first_name = null;
	public ?string $last_name  = null;
}
