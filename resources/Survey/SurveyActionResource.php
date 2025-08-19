<?php

declare(strict_types=1);

namespace app\resources\Survey;

use app\kernel\web\http\resources\JsonResource;
use app\models\SurveyAction;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class SurveyActionResource extends JsonResource
{
	private SurveyAction $resource;

	public function __construct(SurveyAction $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'            => $this->resource->id,
			'survey_id'     => $this->resource->survey_id,
			'target_id'     => $this->resource->target_id,
			'created_by_id' => $this->resource->created_by_id,
			'status'        => $this->resource->status,
			'type'          => $this->resource->type,
			'comment'       => $this->resource->comment,
			'created_at'    => $this->resource->created_at,
			'updated_at'    => $this->resource->updated_at,
			'deleted_at'    => $this->resource->deleted_at,
			'completed_at'  => $this->resource->completed_at,

			'created_by' => UserShortResource::tryMakeArray($this->resource->createdBy)
		];
	}
}