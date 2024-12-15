<?php

declare(strict_types=1);

namespace app\resources\Task;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Task;

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
				'related_by'      => TaskRelationResource::tryMakeArray($this->resource),
				'last_comments'   => TaskCommentResource::collection($this->resource->lastComments),
				'comments_count'  => $this->resource->getCommentsCount(),
				'histories_count' => $this->resource->getHistoriesCount()
			]
		);
	}
}