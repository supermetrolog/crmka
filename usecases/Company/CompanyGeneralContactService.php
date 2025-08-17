<?php

declare(strict_types=1);

namespace app\usecases\Company;

use app\dto\Company\CompanyContactsDto;
use app\dto\Contact\CreateContactDto;
use app\dto\Contact\UpdateContactDto;
use app\dto\Phone\PhoneDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Company;
use app\models\Contact;
use app\usecases\Contact\ContactService;
use Throwable;
use yii\db\StaleObjectException;

class CompanyGeneralContactService
{
	private ContactService $contactService;

	public function __construct(
		ContactService $contactService
	)
	{
		$this->contactService = $contactService;
	}

	/**
	 * @param PhoneDto[] $phoneDtos
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(Company $company, CompanyContactsDto $contactsDto, array $phoneDtos = []): Contact
	{
		$dto = new CreateContactDto([
			'company_id'          => $company->id,
			'type'                => Contact::GENERAL_CONTACT_TYPE,
			'first_name'          => Contact::GENERAL_CONTACT_FIRST_NAME,
			'emails'              => $contactsDto->emails,
			'websites'            => $contactsDto->websites,
			'consultant_id'       => null,
			'middle_name'         => null,
			'last_name'           => null,
			'position'            => null,
			'position_unknown'    => Contact::POSITION_IS_KNOWN,
			'faceToFaceMeeting'   => null,
			'warning'             => null,
			'good'                => null,
			'passive_why'         => null,
			'passive_why_comment' => null,
			'warning_why_comment' => null,
			'isMain'              => null,
			'status'              => Contact::STATUS_ACTIVE
		]);

		$model = $this->contactService->create($dto, $phoneDtos);

		$model->saveOrThrow();

		return $model;
	}


	/**
	 * @param Contact            $model
	 * @param CompanyContactsDto $contactsDto
	 *
	 * @return Contact
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function update(Contact $model, CompanyContactsDto $contactsDto): Contact
	{
		$dto = new UpdateContactDto(
			[
				'first_name'          => $model->first_name ?? Contact::GENERAL_CONTACT_FIRST_NAME,
				'middle_name'         => $model->middle_name,
				'last_name'           => $model->last_name,
				'position'            => $model->position,
				'position_unknown'    => $model->position_unknown,
				'faceToFaceMeeting'   => $model->faceToFaceMeeting,
				'warning'             => $model->warning,
				'good'                => $model->good,
				'passive_why'         => $model->passive_why,
				'passive_why_comment' => $model->passive_why_comment,
				'warning_why_comment' => $model->warning_why_comment,
				'status'              => $model->status,
				'isMain'              => $model->isMain,
				'consultant_id'       => $model->consultant_id,
				'emails'              => $contactsDto->emails,
				'websites'            => $contactsDto->websites
			]
		);

		return $this->contactService->update($model, $dto);
	}

	/**
	 * @param Contact $contact
	 *
	 * @return void
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Contact $contact): void
	{
		$this->contactService->delete($contact);
	}
}