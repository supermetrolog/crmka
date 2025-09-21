<?php

namespace app\events\Request;

use app\events\AbstractEvent;
use app\models\Request;
use app\models\User\User;

class RequestConsultantChangedEvent extends AbstractEvent
{
	private Request $request;
	private User    $oldConsultant;
	private User    $newConsultant;

	public function __construct(Request $request, User $oldConsultant, User $newConsultant)
	{
		$this->request = $request;

		$this->oldConsultant = $oldConsultant;
		$this->newConsultant = $newConsultant;

		parent::__construct();
	}

	public function getRequest(): Request
	{
		return $this->request;
	}

	public function getOldConsultant(): User
	{
		return $this->oldConsultant;
	}

	public function getNewConsultant(): User
	{
		return $this->newConsultant;
	}
}
