<?php

declare(strict_types=1);

namespace app\resources\User;

use app\kernel\web\http\resources\JsonResource;
use app\models\views\UserOnlineView;

class UserOnlineResource extends JsonResource
{
	private UserOnlineView $resource;

	public function __construct(UserOnlineView $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'online_count' => $this->resource->online_count
		];
	}
}