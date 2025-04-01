<?php

declare(strict_types=1);

namespace app\resources\Old\Location;

use app\kernel\web\http\resources\JsonResource;
use app\models\oldDb\location\Region;

class OldLocationRegionResource extends JsonResource
{
	private Region $resource;

	public function __construct(Region $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'title'      => $this->resource->title,
			'title_eng'  => $this->resource->title_eng,
			'order_desc' => $this->resource->order_desc,
			'order_row'  => $this->resource->order_row,
			'cian_id'    => $this->resource->cian_id,
			'exclude'    => $this->resource->exclude,
			'deleted'    => $this->resource->deleted
		];
	}
}