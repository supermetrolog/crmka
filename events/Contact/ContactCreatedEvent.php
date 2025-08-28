<?php

namespace app\events\Contact;


use app\events\AbstractEvent;
use app\models\Contact;

class ContactCreatedEvent extends AbstractEvent
{
	protected Contact $contact;

	public function __construct(Contact $contact)
	{
		parent::__construct();

		$this->contact = $contact;
	}

	public function getContact(): Contact
	{
		return $this->contact;
	}
}
