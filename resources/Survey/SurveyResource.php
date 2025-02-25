<?php

declare(strict_types=1);

namespace app\resources\Survey;

use app\kernel\web\http\resources\JsonResource;
use app\models\Survey;
use app\resources\Contact\ContactResource;
use app\resources\User\UserResource;

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
			'id'             => $this->resource->id,
			'user_id'        => $this->resource->user_id,
			'contact_id'     => $this->resource->contact_id,
			'created_at'     => $this->resource->created_at,
			'updated_at'     => $this->resource->updated_at,
			'chat_member_id' => $this->resource->chat_member_id,

			'user'    => UserResource::make($this->resource->user)->toArray(),
			'contact' => ContactResource::make($this->resource->contact)->toArray(),
		];
	}
}