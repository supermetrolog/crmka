<?php

declare(strict_types=1);

namespace app\resources\Company\PinnedMessage;

use app\kernel\web\http\resources\JsonResource;
use app\models\CompanyPinnedMessage;
use app\resources\ChatMemberMessage\ChatMemberMessageInlineResource;

class CompanyPinnedMessageResource extends JsonResource
{
	private CompanyPinnedMessage $resource;

	public function __construct(CompanyPinnedMessage $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                     => $this->resource->id,
			'company_id'             => $this->resource->company_id,
			'chat_member_message_id' => $this->resource->chat_member_message_id,
			'created_by_id'          => $this->resource->created_by_id,
			'created_at'             => $this->resource->created_at,
			'updated_at'             => $this->resource->updated_at,
			'deleted_at'             => $this->resource->deleted_at,

			'message' => ChatMemberMessageInlineResource::make($this->resource->chatMemberMessage)->toArray(),
		];
	}
}