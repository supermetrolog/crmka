<?php

namespace app\listeners\Company;

use app\dto\Contact\DisableContactDto;
use app\events\Company\DeleteCompanyEvent;
use app\events\Company\DisableCompanyEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\Company\Company;
use app\models\Contact;
use app\usecases\Contact\ContactService;
use Throwable;
use yii\base\Event;


class DeactivateCompanyContactsListener implements EventListenerInterface
{
	private ContactService               $contactService;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(ContactService $contactService, TransactionBeginnerInterface $transactionBeginner)
	{
		$this->contactService      = $contactService;
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @param DisableCompanyEvent|DeleteCompanyEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		if ($event->getDto()->disable_contacts) {
			$this->disableContacts($event->getCompany());
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function disableContacts(Company $company): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$contacts = $company->activeContacts;

			foreach ($contacts as $contact) {
				$this->disableContact($contact);
			}

			$tx->commit();
		} catch (Throwable $e) {
			$tx->rollBack();
			throw $e;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function disableContact(Contact $contact): void
	{
		$this->contactService->markAsPassive(
			$contact,
			new DisableContactDto([
				'passive_why' => Contact::PASSIVE_WHY_COMPANY_DISABLED
			])
		);
	}
}