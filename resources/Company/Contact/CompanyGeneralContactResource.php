<?php

declare(strict_types=1);

namespace app\resources\Company\Contact;

use app\kernel\web\http\resources\JsonResource;
use app\models\Contact;
use app\resources\Contact\Email\ContactEmailResource;
use app\resources\Contact\Phone\ContactPhoneResource;
use app\resources\Contact\Website\ContactWebsiteResource;

class CompanyGeneralContactResource extends JsonResource
{
	private Contact $resource;

	public function __construct(Contact $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'          => $this->resource->id,
			'company_id'  => $this->resource->company_id,
			'first_name'  => $this->resource->first_name,
			'middle_name' => $this->resource->middle_name,
			'last_name'   => $this->resource->last_name,
			'full_name'   => $this->resource->getFullName(),
			'type'        => $this->resource->type,
			'created_at'  => $this->resource->created_at,
			'updated_at'  => $this->resource->updated_at,
			'phones'      => ContactPhoneResource::collection($this->resource->phones),
			'emails'      => ContactEmailResource::collection($this->resource->emails),
			'websites'    => ContactWebsiteResource::collection($this->resource->websites),
		];
	}
}