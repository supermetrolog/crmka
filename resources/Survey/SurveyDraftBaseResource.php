<?php

declare(strict_types=1);

namespace app\resources\Survey;

use app\kernel\web\http\resources\JsonResource;
use app\models\SurveyDraft;

class SurveyDraftBaseResource extends JsonResource
{
	private SurveyDraft $resource;

	public function __construct(SurveyDraft $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'             => $this->resource->id,
			'user_id'        => $this->resource->user_id,
			'chat_member_id' => $this->resource->chat_member_id,
			'created_at'     => $this->resource->created_at,
			'updated_at'     => $this->resource->updated_at,
			'data'           => $this->resource->getParsedData()
		];
	}
}