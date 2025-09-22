<?php

declare(strict_types=1);

namespace app\resources\Task;

use app\kernel\web\http\resources\JsonResource;
use app\models\Task;
use app\models\User\User;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
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
			'title'           => $this->resource->title,
			'status'          => $this->resource->status,
			'start'           => $this->resource->start,
			'end'             => $this->resource->end,
			'type'            => $this->resource->type,
			'created_by_type' => $this->resource->created_by_type,
			'created_by_id'   => $this->resource->created_by_id,
			'created_at'      => $this->resource->created_at,
			'updated_at'      => $this->resource->updated_at,
			'deleted_at'      => $this->resource->deleted_at,
			'impossible_to'   => $this->resource->impossible_to,
			'user'            => UserShortResource::make($this->resource->user)->toArray(),
			'is_viewed'       => $this->resource->isViewed(),
			'viewed_at'       => $this->getViewedAt(),
			'created_by'      => $this->getCreatedBy()->toArray(),
			'tags'            => TaskTagResource::collection($this->resource->tags),
			'observers'       => TaskObserverResource::collection($this->resource->observers),
		];
	}

	private function getCreatedBy(): JsonResource
	{
		$createdBy = $this->resource->createdBy;

		if ($createdBy instanceof User) {
			return new UserShortResource($createdBy);
		}

		throw new UnexpectedValueException('Unknown created by type');
	}

	private function getViewedAt(): ?string
	{
		if (!$this->resource->isViewed()) {
			return null;
		}

		return $this->resource->getViewedAt();
	}
}