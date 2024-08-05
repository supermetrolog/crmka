<?php

declare(strict_types=1);

namespace app\resources\User;

use app\kernel\web\http\resources\JsonResource;
use app\models\User;

class UserResource extends JsonResource
{
	private User $user;

	public function __construct(User $user)
	{
		$this->user = $user;
	}

	public function toArray(): array
	{
		return [
			'id'             => $this->user->id,
			'username'       => $this->user->username,
			'status'         => $this->user->status,
			'created_at'     => $this->user->created_at,
			'updated_at'     => $this->user->updated_at,
			'email'          => $this->user->email,
			'email_username' => $this->user->email_username,
			'role'           => $this->user->role,
			'user_id_old'    => $this->user->user_id_old,
			'userProfile'    => UserProfileResource::make($this->user->userProfile)->toArray()
		];
	}
}