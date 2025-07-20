<?php

namespace app\listeners\Deal;

use app\events\Deal\CreateRequestDealEvent;
use app\helpers\TypeConverterHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\listeners\EventListenerInterface;
use app\models\Request;
use app\usecases\Request\RequestService;
use Throwable;
use yii\base\Event;


class CompleteDealRequestListener implements EventListenerInterface
{
	private RequestService $requestService;

	public function __construct(RequestService $requestService, TransactionBeginnerInterface $transactionBeginner)
	{
		$this->requestService = $requestService;
	}

	/**
	 * @param CreateRequestDealEvent $event
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function handle(Event $event): void
	{
		$dto = $event->getDto();

		if ($dto && TypeConverterHelper::toBool($dto->complete_request)) {
			$this->completeRequest($event->getRequest());
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function completeRequest(Request $request): void
	{
		$this->requestService->markAsCompleted($request);
	}
}