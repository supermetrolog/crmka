<?php

declare(strict_types=1);

namespace app\components\Telegram\Models;

class TMessageEntity extends TModel
{
	protected array $casts = [
		'user' => TUser::class
	];

	public string  $type;
	public int     $offset;
	public int     $length;
	public ?string $url  = null;
	public ?TUser  $user = null;
}
