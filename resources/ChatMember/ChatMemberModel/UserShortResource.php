<?php

declare(strict_types=1);

namespace app\resources\ChatMember\ChatMemberModel;

use app\kernel\web\http\resources\JsonResource;
use app\models\Task;
use app\models\User;
use app\resources\User\UserProfileResource;

class UserShortResource extends JsonResource
{
	private User $user;

	public function __construct(User $user)
	{
		$this->user = $user;
	}

	public function toArray(): array
	{
		return [
			'id'          => $this->user->id,
			'role'        => $this->user->role,
			'created_at'  => $this->user->created_at,
			'updated_at'  => $this->user->updated_at,
			'userProfile' => UserProfileResource::make($this->user->userProfile)->toArray()
		];
	}
}