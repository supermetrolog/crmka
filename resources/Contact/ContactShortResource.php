<?php

declare(strict_types=1);

namespace app\resources\Contact;

use app\kernel\web\http\resources\JsonResource;
use app\models\Contact;

class ContactShortResource extends JsonResource
{
	private Contact $resource;

	public function __construct(Contact $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                  => $this->resource->id,
			'company_id'          => $this->resource->company_id,
			'first_name'          => $this->resource->first_name,
			'middle_name'         => $this->resource->middle_name,
			'last_name'           => $this->resource->last_name,
			'type'                => $this->resource->type,
			'created_at'          => $this->resource->created_at,
			'updated_at'          => $this->resource->updated_at,
			'consultant_id'       => $this->resource->consultant_id,
			'position'            => $this->resource->position,
			'faceToFaceMeeting'   => $this->resource->faceToFaceMeeting,
			'warning'             => $this->resource->warning,
			'good'                => $this->resource->good,
			'status'              => $this->resource->status,
			'passive_why'         => $this->resource->passive_why,
			'passive_why_comment' => $this->resource->passive_why_comment,
			'warning_why_comment' => $this->resource->warning_why_comment,
			'position_unknown'    => $this->resource->position_unknown,
			'isMain'              => $this->resource->isMain,
		];
	}
}