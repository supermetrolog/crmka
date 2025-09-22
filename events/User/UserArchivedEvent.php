<?php

namespace app\events\User;

use app\events\AbstractEvent;
use app\models\User\User;

class UserArchivedEvent extends AbstractEvent
{
	protected User $user;

	public function __construct(User $user)
	{
		$this->user = $user;

		parent::__construct();
	}

	public function getUser(): User
	{
		return $this->user;
	}
}
