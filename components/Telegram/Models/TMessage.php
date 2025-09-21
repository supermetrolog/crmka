<?php

declare(strict_types=1);

namespace app\components\Telegram\Models;

class TMessage extends TModel
{
	protected array $casts = [
		'from'    => TUser::class,
		'contact' => TContact::class,
		'chat'    => TChat::class,
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

	public function setEntities(array $entities): void
	{
		$this->entities = [];

		foreach ($entities as $entity) {
			$this->entities[] = new TMessageEntity($entity);
		}
	}
}
