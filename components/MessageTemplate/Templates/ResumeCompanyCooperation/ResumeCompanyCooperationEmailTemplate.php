<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Templates\ResumeCompanyCooperation;

use app\components\MessageTemplate\Dto\ResumeCompanyCooperationMessageTemplateDto;
use app\components\MessageTemplate\Interfaces\MessageTemplateContextInterface;
use app\components\MessageTemplate\Templates\AbstractEmailTemplate;
use app\helpers\ArrayHelper;
use yii\base\ErrorException;

class ResumeCompanyCooperationEmailTemplate extends AbstractEmailTemplate
{
	protected function getTemplateName(): string
	{
		return 'resume-company-cooperation.twig';
	}

	/**
	 * @param ResumeCompanyCooperationMessageTemplateDto $dto
	 *
	 * @throws ErrorException
	 */
	protected function prepareContext($dto): MessageTemplateContextInterface
	{
		$company = $dto->company;
		$contact = $dto->contact;

		$requests    = $company->requests;
		$hasRequests = ArrayHelper::notEmpty($requests);
		$request     = $requests[0] ?? null;

		$offers    = $company->getOffers()->andWhere(['deleted' => 0, 'c_industry_offers_mix.type_id' => 2])->all();
		$hasOffers = ArrayHelper::notEmpty($offers) && !empty($offers[0]->address);
		$offer     = $offers[0] ?? null;

		$hasObjects = $company->getObjects()
		                      ->andWhere(['is_land' => 0, 'deleted' => 0, 'test_only' => null])
		                      ->count() > 0;

		return new ResumeCompanyCooperationEmailContext([
			'hasOffers'      => $hasOffers,
			'hasRequests'    => ArrayHelper::notEmpty($requests),
			'hasObjects'     => $hasObjects,
			'contactName'    => $contact->getMediumName(),
			'offerAddress'   => $hasOffers ? $offer->address : '',
			'requestSummary' => $hasRequests ? $request->getSummary() : '',
			'user'           => $dto->user,
		]);
	}
}