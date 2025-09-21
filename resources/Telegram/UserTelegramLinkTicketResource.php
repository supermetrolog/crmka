<?php

declare(strict_types=1);

namespace app\resources\Telegram;

use app\kernel\web\http\resources\JsonResource;
use app\models\User\UserTelegramLinkTicket;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class UserTelegramLinkTicketResource extends JsonResource
{
	private UserTelegramLinkTicket $resource;

	public function __construct(UserTelegramLinkTicket $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'          => $this->resource->id,
			'user_id'     => $this->resource->user_id,
			'expires_at'  => $this->resource->expires_at,
			'consumed_at' => $this->resource->consumed_at,
			'created_at'  => $this->resource->created_at,
			'updated_at'  => $this->resource->updated_at,

			'user' => UserShortResource::makeArray($this->resource->user),
		];
	}
}