<?php

declare(strict_types=1);

namespace app\resources\Contact\Comment;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\ContactComment;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class ContactCommentResource extends JsonResource
{
	private ContactComment $resource;

	public function __construct(ContactComment $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'author_id'  => $this->resource->author_id,
			'author'     => UserShortResource::make($this->resource->author)->toArray(),
			'comment'    => $this->resource->comment,
			'created_at' => $this->resource->created_at
		];
	}
}