<?php

declare(strict_types=1);

namespace app\resources\Whatsapp;

use app\kernel\web\http\resources\JsonResource;
use app\models\User\UserWhatsappLink;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class UserWhatsappLinkResource extends JsonResource
{
	private UserWhatsappLink $resource;

	public function __construct(UserWhatsappLink $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                  => $this->resource->id,
			'user_id'             => $this->resource->user_id,
			'whatsapp_profile_id' => $this->resource->whatsapp_profile_id,
			'phone'               => $this->resource->phone,
			'first_name'          => $this->resource->first_name,
			'full_name'           => $this->resource->full_name,
			'push_name'           => $this->resource->push_name,
			'revoked_at'          => $this->resource->revoked_at,
			'created_at'          => $this->resource->created_at,
			'updated_at'          => $this->resource->updated_at,
			'user'                => UserShortResource::makeArray($this->resource->user),
		];
	}
}