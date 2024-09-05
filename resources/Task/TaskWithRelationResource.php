<?php

declare(strict_types=1);

namespace app\resources\Task;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Task;
use app\models\TaskComment;

/**
 * Ресурс с описанием зависимости задачи (информаци о сообщении, о чате, в котором создана задача)
 */
class TaskWithRelationResource extends JsonResource
{
	private Task $resource;

	public function __construct(Task $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return ArrayHelper::merge(
			TaskResource::make($this->resource)->toArray(),
			[
				'related_by'   => TaskRelationResource::tryMake($this->resource)->toArray(),
				'last_comment' => $this->getLastComment()
			]
		);
	}

	public function getLastComment(): ?array
	{
		$lastComment = $this->resource->lastComment;

		if ($lastComment instanceof TaskComment) {
			return TaskCommentResource::make($lastComment)->toArray();
		}

		return null;
	}
}