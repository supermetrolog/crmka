<?php

declare(strict_types=1);

namespace app\resources\Survey;

use app\kernel\web\http\resources\JsonResource;
use app\models\Survey;
use app\resources\Call\CallShortResource;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\ChatMember\ChatMemberShortResource;
use app\resources\Contact\ContactResource;

class SurveyResource extends JsonResource
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
			'related_survey_id' => $this->resource->related_survey_id,
			'updated_at'        => $this->resource->updated_at,
			'completed_at'      => $this->resource->completed_at,
			'chat_member_id'    => $this->resource->chat_member_id,

			'user'       => UserShortResource::make($this->resource->user)->toArray(),
			'contact'    => ContactResource::tryMakeArray($this->resource->contact),
			'chatMember' => ChatMemberShortResource::tryMakeArray($this->resource->chatMember),
			'calls'      => CallShortResource::collection($this->resource->calls)
		];
	}
}