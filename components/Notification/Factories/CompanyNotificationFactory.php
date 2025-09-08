<?php

declare(strict_types=1);

namespace app\components\Notification\Factories;

use app\components\Notification\Builders\NotificationActionBuilder;
use app\components\Notification\Notification;
use app\components\Notification\NotificationAction;
use app\components\Notification\NotificationRelation;
use app\models\Company\Company;

class CompanyNotificationFactory
{
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
		$subject = 'Компании';

		$message = sprintf('За вами закреплена компания "%s" (#%d)', $company->getShortName(), $company->id);

		$actions   = [$this->makeNavigateAction($company)];
		$relations = [NotificationRelation::from($company)];

		return new Notification($subject, $message, null, $actions, $relations);
	}

	public function reassigned(Company $company): Notification
	{
		$subject = 'Компании';

		$message = sprintf('От вас откреплена компания "%s" (#%d)', $company->getShortName(), $company->id);

		$actions   = [$this->makeNavigateAction($company)];
		$relations = [NotificationRelation::from($company)];

		return new Notification($subject, $message, null, $actions, $relations);
	}
}