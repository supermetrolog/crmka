<?php

declare(strict_types=1);

namespace app\components\Integrations\Telegram\Models;

use app\components\Integrations\IntegrationModel;
use app\helpers\ArrayHelper;

class IntegrationMessage extends IntegrationModel
{
	protected array $casts = [
		'from'     => IntegrationUser::class,
		'contact'  => IntegrationContact::class,
		'chat'     => IntegrationChat::class,
		'entities' => [IntegrationMessageEntity::class],
	];

	public int              $message_id;
	public ?int             $message_thread_id = null;
	public ?IntegrationUser $from              = null;
	public int              $date;
	public ?int             $edit_date         = null;
	public ?bool            $is_from_offline   = null;
	public ?string          $text              = null;

	/** @var IntegrationMessageEntity[] */
	public ?array $entities = [];

	public ?IntegrationContact $contact = null;
	public IntegrationChat     $chat;

	public function hasEntityType(string $type): bool
	{
		return ArrayHelper::any($this->entities, static fn($entity) => $entity->type === $type);
	}
}
