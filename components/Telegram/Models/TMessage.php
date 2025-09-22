<?php

declare(strict_types=1);

namespace app\components\Telegram\Models;

use app\helpers\ArrayHelper;

class TMessage extends TModel
{
	protected array $casts = [
		'from'     => TUser::class,
		'contact'  => TContact::class,
		'chat'     => TChat::class,
		'entities' => [TMessageEntity::class],
	];

	public int     $message_id;
	public ?int    $message_thread_id = null;
	public ?TUser  $from              = null;
	public int     $date;
	public ?int    $edit_date         = null;
	public ?bool   $is_from_offline   = null;
	public ?string $text              = null;

	/** @var TMessageEntity[] */
	public ?array $entities = [];

	public ?TContact $contact = null;
	public TChat     $chat;

	public function hasEntityType(string $type): bool
	{
		return ArrayHelper::any($this->entities, static fn($entity) => $entity->type === $type);
	}
}
