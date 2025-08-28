<?php

namespace app\listeners\Contact;

use app\events\Contact\ContactStatusChangedEvent;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\usecases\Company\CompanyStatusService;
use ErrorException;
use Throwable;


class ContactCreatedListener implements EventListenerInterface
{
	private CompanyStatusService $companyStatusService;

	public function __construct(CompanyStatusService $companyStatusService)
	{
		$this->companyStatusService = $companyStatusService;
	}

	/**
	 * @param ContactStatusChangedEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 * @throws ErrorException
	 */
	public function handle($event): void
	{
		$contact = $event->getContact();

		$this->companyStatusService->activeIfNeeded($contact->company);
	}
}