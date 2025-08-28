<?php

namespace app\listeners\Company;

use app\dto\Request\PassiveRequestDto;
use app\enum\Request\RequestPassiveWhyEnum;
use app\events\Company\DeleteCompanyEvent;
use app\events\Company\DisableCompanyEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\Company\Company;
use app\models\Request;
use app\usecases\Request\RequestService;
use Throwable;
use yii\base\Event;


class DeactivateCompanyRequestsListener implements EventListenerInterface
{
	private RequestService               $requestService;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(RequestService $requestService, TransactionBeginnerInterface $transactionBeginner)
	{
		$this->requestService      = $requestService;
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
		if ($event->getDto()->disable_requests) {
			$this->disableRequests($event->getCompany());
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function disableRequests(Company $company): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$activeRequests = $company->activeRequests;

			foreach ($activeRequests as $request) {
				$this->disableRequest($request);
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
	private function disableRequest(Request $request): void
	{
		$this->requestService->markAsPassive($request, new PassiveRequestDto([
			'passive_why'         => RequestPassiveWhyEnum::OTHER,
			'passive_why_comment' => null
		]));
	}
}