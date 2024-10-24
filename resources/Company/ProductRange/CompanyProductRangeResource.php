<?php

declare(strict_types=1);

namespace app\resources\Company\ProductRange;

use app\kernel\web\http\resources\JsonResource;
use app\models\Productrange;

class CompanyProductRangeResource extends JsonResource
{
	private Productrange $resource;

	public function __construct(Productrange $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'      => $this->resource->id,
			'product' => $this->resource->product
		];
	}
}