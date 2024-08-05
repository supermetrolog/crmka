<?php

declare(strict_types=1);

namespace app\resources\ChatMember;

use app\kernel\web\http\resources\JsonResource;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageTag;
use app\resources\Contact\ContactShortResource;
use app\resources\TaskResource;

class ChatMemberMessageTagResource extends JsonResource
{
	private ChatMemberMessageTag $resource;

	public function __construct(ChatMemberMessageTag $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'   => $this->resource->id,
			'name' => $this->resource->name,
		];
	}
}