<?php

namespace app\events\Contact;

use app\events\AbstractEvent;
use app\models\Company;
use app\models\Contact;

class UpdateContactEvent extends AbstractEvent
{
	public Contact $contact;

	public function __construct(Contact $contact)
	{
		$this->contact = $contact;

		parent::__construct();
	}

	public function getContact(): Contact
	{
		return $this->contact;
	}

	public function getContactCompany(): ?Company
	{
		$contact = $this->getContact();

		if (!empty($contact->company_id)) {
			return $contact->company;
		}

		return null;
	}
}
