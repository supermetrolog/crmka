<?php

namespace app\listeners\Company;

use app\dto\Request\ChangeRequestConsultantDto;
use app\events\Company\ChangeConsultantCompanyEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\Company\Company;
use app\models\Request;
use app\models\User\User;
use app\usecases\Request\RequestService;
use Throwable;
use yii\base\Event;


class ChangeCompanyRequestsConsultantListener implements EventListenerInterface
{
	private RequestService               $requestService;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(RequestService $requestService, TransactionBeginnerInterface $transactionBeginner)
	{
		$this->requestService      = $requestService;
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @param ChangeConsultantCompanyEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		if ($event->getDto()->change_requests_consultants) {
			$this->changeRequestsConsultants($event->getCompany());
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function changeRequestsConsultants(Company $company): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$activeRequests = $company->activeRequests;

			foreach ($activeRequests as $request) {
				$this->changeRequestsConsultant($request, $company->consultant);
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
	private function changeRequestsConsultant(Request $request, User $consultant): void
	{
		if ($request->consultant_id === $consultant->id) {
			return;
		}

		$this->requestService->changeConsultant($request, new ChangeRequestConsultantDto(['consultant' => $consultant]));
	}
}