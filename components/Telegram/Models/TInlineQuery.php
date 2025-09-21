<?php

declare(strict_types=1);

namespace app\components\Telegram\Models;

class TInlineQuery extends TModel
{
	protected array $casts = [
		'from' => TUser::class
	];

	public int    $id;
	public ?TUser $from = null;
	public string $query;
	public string $offset;
}
