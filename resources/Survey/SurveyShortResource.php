<?php

declare(strict_types=1);

namespace app\resources\Survey;

use app\kernel\web\http\resources\JsonResource;
use app\models\Survey;

class SurveyShortResource extends JsonResource
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
			'type'              => $this->resource->type,
			'status'            => $this->resource->status,
			'created_at'        => $this->resource->created_at,
			'updated_at'        => $this->resource->updated_at,
			'completed_at'      => $this->resource->completed_at,
			'chat_member_id'    => $this->resource->chat_member_id,
			'related_survey_id' => $this->resource->related_survey_id
		];
	}
}