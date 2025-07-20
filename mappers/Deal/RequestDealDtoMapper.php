<?php

namespace app\mappers\Deal;

use app\dto\Deal\CreateRequestDealDto;
use app\helpers\DateTimeHelper;
use app\mappers\AbstractDtoMapper;
use app\models\forms\Deal\RequestDealForm;
use app\models\Objects;
use app\repositories\CompanyRepository;
use app\repositories\ComplexRepository;
use app\repositories\RequestRepository;
use app\repositories\UserRepository;
use Exception;

class RequestDealDtoMapper extends AbstractDtoMapper
{
	protected CompanyRepository $companyRepository;
	protected RequestRepository $requestRepository;
	protected UserRepository    $userRepository;
	protected ComplexRepository $complexRepository;

	public function __construct(
		CompanyRepository $companyRepository,
		RequestRepository $requestRepository,
		UserRepository $userRepository,
		ComplexRepository $complexRepository
	)
	{
		$this->companyRepository = $companyRepository;
		$this->requestRepository = $requestRepository;
		$this->userRepository    = $userRepository;
		$this->complexRepository = $complexRepository;
	}

	/**
	 * @throws Exception
	 */
	public function fromForm(RequestDealForm $form): CreateRequestDealDto
	{
		return new CreateRequestDealDto([
			'name'               => $form->name,
			'is_our'             => $form->is_our,
			'is_competitor'      => $form->is_competitor,
			'type_id'            => $form->type_id,
			'original_id'        => $form->original_id,
			'clientLegalEntity'  => $form->clientLegalEntity,
			'description'        => $form->description,
			'formOfOrganization' => $form->formOfOrganization,
			'area'               => $form->area,
			'floorPrice'         => $form->floorPrice,
			'contractTerm'       => $form->contractTerm,
			'dealDate'           => DateTimeHelper::tryMake($form->dealDate),
			'visual_id'          => $form->visual_id,

			'object' => Objects::find()->byId((int)$form->object_id)->one(),

			'company'    => $this->findOrNull($this->companyRepository, $form->company_id),
			'competitor' => $this->findOrNull($this->companyRepository, $form->competitor_company_id),
			'complex'    => $this->findOrNull($this->complexRepository, $form->complex_id),
			'consultant' => $this->findOrNull($this->userRepository, $form->consultant_id),

			'complete_request' => $form->complete_request
		]);
	}
}