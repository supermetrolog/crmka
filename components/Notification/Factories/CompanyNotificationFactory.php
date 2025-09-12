<?php

declare(strict_types=1);

namespace app\components\Notification\Factories;

use app\components\Notification\Builders\NotificationActionBuilder;
use app\components\Notification\Notification;
use app\components\Notification\NotificationAction;
use app\components\Notification\NotificationRelation;
use app\enum\Notification\UserNotificationTemplateKindEnum;
use app\models\Company\Company;
use app\repositories\UserNotificationTemplateRepository;

class CompanyNotificationFactory
{
	protected UserNotificationTemplateRepository $templateRepository;

	public function __construct(UserNotificationTemplateRepository $templateRepository)
	{
		$this->templateRepository = $templateRepository;
	}

	protected function makeNavigateAction(Company $company): NotificationAction
	{
		return NotificationActionBuilder::navigateToRoute(
			'company.view',
			['id' => $company->id],
			null,
			"/companies/$company->id"
		)
		                                ->label('Перейти к компании')
		                                ->code('navigate_to_company')
		                                ->build();
	}

	public function assigned(Company $company): Notification
	{
		$subject = 'Назначение консультантом компании';

		$message = sprintf('За вами закреплена компания "%s" (#%d)', $company->getShortName(), $company->id);

		$template = $this->templateRepository->findOneByKind(UserNotificationTemplateKindEnum::CHANGE_COMPANY_CONSULTANT);

		$actions   = [$this->makeNavigateAction($company)];
		$relations = [NotificationRelation::from($company)];

		return new Notification($subject, $message, $template, $actions, $relations);
	}

	public function reassigned(Company $company): Notification
	{
		$subject = 'Открепление от компании';

		$message = sprintf('От вас откреплена компания "%s" (#%d)', $company->getShortName(), $company->id);

		$template = $this->templateRepository->findOneByKind(UserNotificationTemplateKindEnum::CHANGE_COMPANY_CONSULTANT);

		$actions   = [$this->makeNavigateAction($company)];
		$relations = [NotificationRelation::from($company)];

		return new Notification($subject, $message, $template, $actions, $relations);
	}
}