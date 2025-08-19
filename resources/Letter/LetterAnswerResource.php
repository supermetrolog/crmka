<?php

declare(strict_types=1);

namespace app\resources\Letter;

use app\kernel\web\http\resources\JsonResource;
use app\models\LetterContactAnswer;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class LetterAnswerResource extends JsonResource
{
	private LetterContactAnswer $resource;

	public function __construct(LetterContactAnswer $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                 => $this->resource->id,
			'letter_contact_id'  => $this->resource->letter_contact_id,
			'marked_by_id'       => $this->resource->marked_by_id,
			'marked_at'          => $this->resource->marked_at,
			'type'               => $this->resource->type,
			'comment'            => $this->resource->comment,
			'related_message_id' => $this->resource->related_message_id,
			'deleted_at'         => $this->resource->deleted_at,

			'marked_by' => UserShortResource::tryMakeArray($this->resource->markedBy),
		];
	}
}
