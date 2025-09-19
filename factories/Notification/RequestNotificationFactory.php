<?php

declare(strict_types=1);

namespace app\factories\Notification;

use app\components\Notification\Builders\NotificationActionBuilder;
use app\components\Notification\Notification;
use app\components\Notification\NotificationAction;
use app\components\Notification\NotificationRelation;
use app\enum\Notification\UserNotificationTemplateKindEnum;
use app\models\Request;
use app\models\User;
use app\repositories\UserNotificationTemplateRepository;

class RequestNotificationFactory
{
	protected UserNotificationTemplateRepository $templateRepository;

	public function __construct(UserNotificationTemplateRepository $templateRepository)
	{
		$this->templateRepository = $templateRepository;
	}

	protected function makeCompanyNavigateAction(Request $request, int $order = 0): NotificationAction
	{
		return NotificationActionBuilder::navigateToRoute(
			'company.view',
			['id' => $request->company_id],
			null,
			"/companies/$request->company_id"
		)
		                                ->label('Перейти к компании')
		                                ->code('navigate_to_company')
		                                ->order($order)
		                                ->build();
	}

	protected function makeRequestNavigateAction(Request $request, int $order = 0): NotificationAction
	{
		return NotificationActionBuilder::navigateToRoute(
			'company.view',
			['id' => $request->company_id],
			['selected_request_id' => $request->id],
			"/companies/$request->company_id"
		)
		                                ->label('Перейти к запросу')
		                                ->code('navigate_to_company_request')
		                                ->order($order)
		                                ->build();
	}

	protected function makeTimelineNavigateAction(Request $request, User $consultant, int $order = 0): NotificationAction
	{
		return NotificationActionBuilder::navigateToRoute(
			'company.view',
			['id' => $request->company_id],
			[
				'request_id'    => $request->id,
				'consultant_id' => $consultant->id
			],
			"/companies/$request->company_id"
		)
		                                ->label('Открыть таймлайн')
		                                ->code('navigate_to_consultant_timeline')
		                                ->order($order)
		                                ->build();
	}

	public function assigned(Request $request): Notification
	{
		$subject = 'Назначение запроса';

		$message = sprintf('За вами закреплен запрос "%s" (#%d) в компании "%s" (#%d)', $request->getFormatName(), $request->id, $request->company->getShortName(), $request->company_id);

		$template = $this->templateRepository->findOneByKind(UserNotificationTemplateKindEnum::CHANGE_REQUEST_CONSULTANT);

		$actions = [
			$this->makeCompanyNavigateAction($request, 1),
			$this->makeTimelineNavigateAction($request, $request->consultant, 2),
			$this->makeRequestNavigateAction($request, 3),
		];

		$relations = [
			NotificationRelation::from($request),
			NotificationRelation::from($request->company),
		];

		return new Notification($subject, $message, $template, $actions, $relations);
	}

	public function reassigned(Request $request, User $oldConsultant): Notification
	{
		$subject = 'Открепление от запроса';

		$message = sprintf('От вас откреплен запрос "%s" (#%d) в компании "%s" (#%d)', $request->getFormatName(), $request->id, $request->company->getShortName(), $request->company_id);

		$template = $this->templateRepository->findOneByKind(UserNotificationTemplateKindEnum::CHANGE_REQUEST_CONSULTANT);

		$actions = [
			$this->makeCompanyNavigateAction($request, 1),
			$this->makeTimelineNavigateAction($request, $oldConsultant, 2),
			$this->makeRequestNavigateAction($request, 3),
		];

		$relations = [
			NotificationRelation::from($request),
			NotificationRelation::from($request->company),
		];

		return new Notification($subject, $message, $template, $actions, $relations);
	}
}