<?php

declare(strict_types=1);

namespace app\resources\TaskRelationEntity;

use app\kernel\web\http\resources\JsonResource;
use app\models\Contact;
use app\resources\Company\CompanyShortResource;
use app\resources\Contact\Email\ContactEmailResource;
use app\resources\Contact\WayOfInforming\ContactWayOfInformingResource;
use app\resources\Phone\PhoneResource;

class TaskRelationEntityContactResource extends JsonResource
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
			'full_name'           => $this->resource->getFullName(),
			'type'                => $this->resource->type,
			'consultant_id'       => $this->resource->consultant_id,
			'position_id'         => $this->resource->position_id,
			'faceToFaceMeeting'   => $this->resource->faceToFaceMeeting,
			'warning'             => $this->resource->warning,
			'good'                => $this->resource->good,
			'status'              => $this->resource->status,
			'passive_why'         => $this->resource->passive_why,
			'passive_why_comment' => $this->resource->passive_why_comment,
			'warning_why_comment' => $this->resource->warning_why_comment,
			'position_unknown'    => $this->resource->position_unknown,
			'isMain'              => $this->resource->isMain,

			'emails'          => ContactEmailResource::collection($this->resource->emails),
			'phones'          => PhoneResource::collection($this->resource->phones),
			'wayOfInformings' => ContactWayOfInformingResource::collection($this->resource->wayOfInformings),
			'company'         => CompanyShortResource::tryMakeArray($this->resource->company)

		];
	}
}