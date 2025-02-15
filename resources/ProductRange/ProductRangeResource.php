<?php

declare(strict_types=1);

namespace app\resources\ProductRange;

use app\kernel\web\http\resources\JsonResource;
use app\models\Productrange;

class ProductRangeResource extends JsonResource
{
	private Productrange $resource;

	public function __construct(Productrange $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'product' => $this->resource->getProductName()
		];
	}
}