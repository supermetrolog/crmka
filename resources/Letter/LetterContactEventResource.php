<?php

declare(strict_types=1);

namespace app\resources\Letter;

use app\kernel\web\http\resources\JsonResource;
use app\models\letter\LetterContactEvent;

class LetterContactEventResource extends JsonResource
{
	private LetterContactEvent $resource;

	public function __construct(LetterContactEvent $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                => $this->resource->id,
			'letter_contact_id' => $this->resource->letter_contact_id,
			'event_type'        => $this->resource->event_type,
			'label'             => $this->resource->getEventTypeLabel(),
			'ip'                => $this->resource->ip,
			'user_agent'        => $this->resource->user_agent,
			'created_at'        => $this->resource->created_at
		];
	}
}
