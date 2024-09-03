<?php

declare(strict_types=1);

namespace app\resources\Task;

use app\kernel\web\http\resources\JsonResource;
use app\models\views\TaskStatusStatisticView;

class TaskStatusStatisticResource extends JsonResource
{
	private TaskStatusStatisticView $resource;

	public function __construct(TaskStatusStatisticView $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'total'      => $this->resource->total,
			'created'    => $this->resource->created,
			'accepted'   => $this->resource->accepted,
			'done'       => $this->resource->done,
			'impossible' => $this->resource->impossible
		];
	}
}