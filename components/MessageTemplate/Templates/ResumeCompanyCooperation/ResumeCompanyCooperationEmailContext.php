<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Templates\ResumeCompanyCooperation;

use app\components\MessageTemplate\Templates\AbstractTemplateContext;
use app\models\User;

class ResumeCompanyCooperationEmailContext extends AbstractTemplateContext
{
	public bool   $hasOffers;
	public bool   $hasRequests;
	public bool   $hasObjects;
	public string $contactName;
	public string $offerAddress;
	public string $requestSummary;
	public User   $user;

	public function toArray(): array
	{
		return [
			'hasOffers'      => $this->hasOffers,
			'hasRequests'    => $this->hasRequests,
			'hasObjects'     => $this->hasObjects,
			'contactName'    => $this->contactName,
			'offerAddress'   => $this->offerAddress,
			'requestSummary' => $this->requestSummary,
			'user'           => $this->user
		];
	}
}