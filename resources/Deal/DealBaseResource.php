<?php

declare(strict_types=1);

namespace app\resources\Deal;

use app\kernel\web\http\resources\JsonResource;
use app\models\Deal;

class DealBaseResource extends JsonResource
{
	private Deal $resource;

	public function __construct(Deal $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                    => $this->resource->id,
			'company_id'            => $this->resource->company_id,
			'request_id'            => $this->resource->request_id,
			'consultant_id'         => $this->resource->consultant_id,
			'area'                  => $this->resource->area,
			'floorPrice'            => $this->resource->floorPrice,
			'clientLegalEntity'     => $this->resource->clientLegalEntity,
			'description'           => $this->resource->description,
			'name'                  => $this->resource->name,
			'object_id'             => $this->resource->object_id,
			'complex_id'            => $this->resource->complex_id,
			'is_our'                => $this->resource->is_our,
			'is_competitor'         => $this->resource->is_competitor,
			'competitor_company_id' => $this->resource->competitor_company_id,
			'type_id'               => $this->resource->type_id,
			'dealDate'              => $this->resource->dealDate,
			'contractTerm'          => $this->resource->contractTerm,
			'formOfOrganization'    => $this->resource->formOfOrganization,
			'original_id'           => $this->resource->original_id,
			'visual_id'             => $this->resource->visual_id,
			'status'                => $this->resource->status,
			'created_at'            => $this->resource->created_at,
			'updated_at'            => $this->resource->updated_at,
		];
	}
}