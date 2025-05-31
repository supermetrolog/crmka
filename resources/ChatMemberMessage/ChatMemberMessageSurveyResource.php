<?php

declare(strict_types=1);

namespace app\resources\ChatMemberMessage;

use app\kernel\web\http\resources\JsonResource;
use app\models\Survey;
use app\resources\ChatMember\ChatMemberBaseResource;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\Contact\ContactShortResource;

class ChatMemberMessageSurveyResource extends JsonResource
{
	private Survey $resource;

	public function __construct(Survey $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{

		return [
			'id'                => $this->resource->id,
			'user_id'           => $this->resource->user_id,
			'contact_id'        => $this->resource->contact_id,
			'status'            => $this->resource->status,
			'type'              => $this->resource->type,
			'created_at'        => $this->resource->created_at,
			'updated_at'        => $this->resource->updated_at,
			'completed_at'      => $this->resource->completed_at,
			'chat_member_id'    => $this->resource->chat_member_id,
			'related_survey_id' => $this->resource->related_survey_id,

			'user'        => UserShortResource::make($this->resource->user)->toArray(),
			'contact'     => ContactShortResource::tryMakeArray($this->resource->contact),
			'chat_member' => ChatMemberBaseResource::make($this->resource->chatMember)->toArray(),
		];
	}
}