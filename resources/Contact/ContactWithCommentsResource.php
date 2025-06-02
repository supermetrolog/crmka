<?php

declare(strict_types=1);

namespace app\resources\Contact;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Contact;
use app\resources\Call\CallShortResource;
use app\resources\Contact\Comment\ContactCommentResource;

class ContactWithCommentsResource extends JsonResource
{
	private Contact $resource;

	public function __construct(Contact $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return ArrayHelper::merge(
			ContactResource::make($this->resource)->toArray(),
			[
				'comments' => ContactCommentResource::collection($this->resource->contactComments),
				'calls'    => CallShortResource::collection($this->resource->calls)
			]
		);
	}
}