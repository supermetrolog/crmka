<?php

declare(strict_types=1);

namespace app\resources\Task;

use app\kernel\web\http\resources\JsonResource;
use app\models\views\TaskRelationStatisticView;

class TaskRelationStatisticResource extends JsonResource
{
	private TaskRelationStatisticView $resource;

	public function __construct(TaskRelationStatisticView $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'by_user'       => $this->resource->by_user,
			'by_created_by' => $this->resource->by_created_by,
			'by_observer'   => $this->resource->by_observer
		];
	}
}