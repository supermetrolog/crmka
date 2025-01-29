<?php

namespace app\listeners\Company;

use app\events\Contact\CreateContactEvent;
use app\events\Contact\UpdateContactEvent;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\Company;
use app\models\Contact;
use app\usecases\Company\CompanyService;
use Throwable;
use yii\base\Event;


class ChangeCompanyStatusListener implements EventListenerInterface
{
	private CompanyService $companyService;

	public function __construct(CompanyService $companyService)
	{
		$this->companyService = $companyService;
	}

	/**
	 * @param CreateContactEvent|UpdateContactEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$contact = $event->getContact();
		$company = $event->getContactCompany();

		if ($company) {
			$this->updateCompanyStatusByContact($company, $contact);
		}
	}

	/**
	 * @throws SaveModelException
	 */
	private function updateCompanyStatusByContact(Company $company, Contact $contact): void
	{
		if ($company->isWithoutActiveContacts()) {
			if ($contact->isDefaultType() && $contact->isActive()) {
				$this->markCompanyAsActive($company);
			}
		} else {
			if ($company->isActive() && $contact->isPassive() && !$company->hasActiveContacts()) {
				$this->markCompanyAsWithoutActiveContacts($company);
			}
		}
	}

	/**
	 * @throws SaveModelException
	 */
	private function markCompanyAsActive(Company $company): void
	{
		$this->companyService->changeStatus($company, Company::STATUS_ACTIVE);
	}

	/**
	 * @throws SaveModelException
	 */
	private function markCompanyAsWithoutActiveContacts(Company $company): void
	{
		$this->companyService->changeStatus($company, Company::STATUS_WITHOUT_ACTIVE_CONTACTS);
	}
}