<?php

namespace app\resources\Timeline;

use app\kernel\web\http\resources\JsonResource;
use app\models\Timeline;

class TimelineViewResource extends JsonResource
{
	private ?Timeline $timeline;

	/** @var Timeline[] */
	private array $requestTimelines;

	public function __construct(?Timeline $timeline = null, array $requestTimelines = [])
	{
		$this->timeline         = $timeline;
		$this->requestTimelines = $requestTimelines;
	}

	public function toArray(): array
	{
		return
			[
				'timeline'          => TimelineFullResource::tryMakeArray($this->timeline),
				'request_timelines' => TimelineBaseResource::collection($this->requestTimelines)
			];
	}
}