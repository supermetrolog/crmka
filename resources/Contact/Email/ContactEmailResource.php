<?php

declare(strict_types=1);

namespace app\resources\Contact\Email;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\Email;

class ContactEmailResource extends JsonResource
{
	private Email $resource;

	public function __construct(Email $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'     => $this->resource->id,
			'email'  => $this->resource->email,
			'isMain' => $this->resource->isMain
		];
	}
}