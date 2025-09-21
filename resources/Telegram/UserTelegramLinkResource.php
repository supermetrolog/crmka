<?php

declare(strict_types=1);

namespace app\resources\Telegram;

use app\kernel\web\http\resources\JsonResource;
use app\models\User\UserTelegramLink;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class UserTelegramLinkResource extends JsonResource
{
	private UserTelegramLink $resource;

	public function __construct(UserTelegramLink $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'               => $this->resource->id,
			'user_id'          => $this->resource->user_id,
			'telegram_user_id' => $this->resource->telegram_user_id,
			'chat_id'          => $this->resource->chat_id,
			'username'         => $this->resource->username,
			'first_name'       => $this->resource->first_name,
			'last_name'        => $this->resource->last_name,
			'is_enabled'       => $this->resource->is_enabled,
			'created_at'       => $this->resource->created_at,
			'updated_at'       => $this->resource->updated_at,

			'user' => UserShortResource::makeArray($this->resource->user),
		];
	}
}