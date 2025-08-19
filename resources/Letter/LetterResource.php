<?php

declare(strict_types=1);

namespace app\resources\Letter;

use app\kernel\web\http\resources\JsonResource;
use app\models\letter\Letter;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class LetterResource extends JsonResource
{
	private Letter $resource;

	public function __construct(Letter $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'              => $this->resource->id,
			'user_id'         => $this->resource->user_id,
			'company_id'      => $this->resource->company_id,
			'type'            => $this->resource->type,
			'shipping_method' => $this->resource->shipping_method,
			'sender_email'    => $this->resource->sender_email,
			'subject'         => $this->resource->subject,
			'body'            => $this->resource->body,
			'created_at'      => $this->resource->created_at,
			'status'          => $this->resource->status,

			'user' => UserShortResource::makeArray($this->resource->user)
		];
	}
}
