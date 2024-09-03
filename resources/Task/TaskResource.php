<?php

declare(strict_types=1);

namespace app\resources\Task;

use app\kernel\web\http\resources\JsonResource;
use app\models\Task;
use app\models\User;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\User\UserResource;
use UnexpectedValueException;

class TaskResource extends JsonResource
{
	private Task $resource;

	public function __construct(Task $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'              => $this->resource->id,
			'user_id'         => $this->resource->user_id,
			'message'         => $this->resource->message,
			'status'          => $this->resource->status,
			'start'           => $this->resource->start,
			'end'             => $this->resource->end,
			'created_by_type' => $this->resource->created_by_type,
			'created_by_id'   => $this->resource->created_by_id,
			'created_at'      => $this->resource->created_at,
			'updated_at'      => $this->resource->updated_at,
			'deleted_at'      => $this->resource->deleted_at,
			'impossible_to'   => $this->resource->impossible_to,
			'user'            => UserShortResource::make($this->resource->user)->toArray(),
			'is_viewed'       => $this->isViewed(),
			'created_by'      => $this->getCreatedBy()->toArray(),
			'tags'            => TaskTagResource::collection($this->resource->tags),
			'observers'       => TaskObserverResource::collection($this->resource->observers),
		];
	}

	private function getCreatedBy(): JsonResource
	{
		$createdBy = $this->resource->createdBy;

		if ($createdBy instanceof User) {
			return new UserResource($createdBy);
		}

		throw new UnexpectedValueException('Unknown created by type');
	}

	private function isViewed(): bool
	{
		$targetUserObserver = $this->resource->targetUserObserver;

		if ($targetUserObserver === null) {
			return false;
		}

		return $targetUserObserver->viewed_at !== null;
	}
}