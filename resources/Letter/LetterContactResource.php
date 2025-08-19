<?php

declare(strict_types=1);

namespace app\resources\Letter;

use app\kernel\web\http\resources\JsonResource;
use app\models\letter\LetterContact;

class LetterContactResource extends JsonResource
{
	private LetterContact $resource;

	public function __construct(LetterContact $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'letter_id'  => $this->resource->letter_id,
			'contact_id' => $this->resource->contact_id,
			'phone'      => $this->resource->phone,
			'email'      => $this->resource->email,

			'letter'  => LetterResource::makeArray($this->resource->letter),
			'answers' => LetterAnswerResource::collection($this->resource->answers),
		];
	}
}
