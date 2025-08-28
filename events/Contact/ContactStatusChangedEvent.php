<?php

namespace app\events\Contact;


use app\events\AbstractEvent;
use app\models\Contact;
use app\models\User;

class ContactStatusChangedEvent extends AbstractEvent
{
	protected Contact $contact;
	protected int     $oldStatus;
	protected int     $newStatus;
	protected ?User   $initiator;

	public function __construct(Contact $contact, int $oldStatus, int $newStatus, ?User $initiator = null)
	{
		parent::__construct();

		$this->contact   = $contact;
		$this->initiator = $initiator;
		$this->oldStatus = $oldStatus;
		$this->newStatus = $newStatus;
	}

	public function getContact(): Contact
	{
		return $this->contact;
	}

	public function getInitiator(): ?User
	{
		return $this->initiator;
	}

	public function getOldStatus(): int
	{
		return $this->oldStatus;
	}

	public function getNewStatus(): int
	{
		return $this->newStatus;
	}
}
