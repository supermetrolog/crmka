<?php

declare(strict_types=1);

namespace app\usecases\Contact;

use app\dto\Contact\CreateContactDto;
use app\dto\Contact\DisableContactDto;
use app\dto\Contact\TransferContactToCompanyDto;
use app\dto\Contact\UpdateContactDto;
use app\dto\Phone\PhoneDto;
use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\mappers\Contact\CreateContactDtoMapper;
use app\mappers\Phone\PhoneDtoMapper;
use app\models\Contact;
use app\models\miniModels\Email;
use app\models\miniModels\WayOfInforming;
use app\models\miniModels\Website;
use app\usecases\Phone\PhoneService;
use Throwable;
use yii\db\StaleObjectException;

class ContactService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private PhoneService                 $phoneService;
	private PhoneDtoMapper               $phoneDtoMapper;
	private CreateContactDtoMapper       $createContactDtoMapper;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		PhoneService $phoneService,
		PhoneDtoMapper $phoneDtoMapper,
		CreateContactDtoMapper $createContactDtoMapper
	)
	{
		$this->transactionBeginner    = $transactionBeginner;
		$this->phoneService           = $phoneService;
		$this->phoneDtoMapper         = $phoneDtoMapper;
		$this->createContactDtoMapper = $createContactDtoMapper;
	}

	/**
	 * @param PhoneDto[] $phoneDtos
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CreateContactDto $dto, array $phoneDtos = []): Contact
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model = new Contact([
				'company_id'          => $dto->company_id,
				'first_name'          => $dto->first_name,
				'middle_name'         => $dto->middle_name,
				'last_name'           => $dto->last_name,
				'position_id'         => $dto->position_id,
				'position_unknown'    => $dto->position_unknown,
				'faceToFaceMeeting'   => $dto->faceToFaceMeeting,
				'warning'             => $dto->warning,
				'good'                => $dto->good,
				'passive_why'         => $dto->passive_why,
				'passive_why_comment' => $dto->passive_why_comment,
				'warning_why_comment' => $dto->warning_why_comment,
				'type'                => $dto->type,
				'status'              => $dto->status,
				'consultant_id'       => $dto->consultant_id
			]);

			$model->saveOrThrow();

			$model->createManyMiniModels([
				Email::class          => $dto->emails,
				Website::class        => $dto->websites,
				WayOfInforming::class => $dto->wayOfInformings
			]);

			$this->createPhones($model, $phoneDtos);

			if ($dto->isMain) {
				$this->setMainContact($model);
			}

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @param PhoneDto[] $dtos
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function createPhones(Contact $model, array $dtos): void
	{
		$tx = $this->transactionBeginner->begin();

		try {

			foreach ($dtos as $phoneDto) {
				$this->phoneService->createForContact($model, $phoneDto);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}


	/**
	 * @param Contact $model
	 *
	 * @return Contact
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function setMainContact(Contact $model): Contact
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$currentMainContacts = $model->getRelatedContacts()->main()->all();

			foreach ($currentMainContacts as $contact) {
				$contact->isMain = null;
				$contact->saveOrThrow();
			}

			$model->isMain = Contact::IS_MAIN_CONTACT;
			$model->saveOrThrow();

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}


	/**
	 * @param Contact          $model
	 * @param UpdateContactDto $dto
	 *
	 * @return Contact
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function update(Contact $model, UpdateContactDto $dto): Contact
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$isMainContact = $model->isMain;

			$model->load([
				'first_name'          => $dto->first_name,
				'middle_name'         => $dto->middle_name,
				'last_name'           => $dto->last_name,
				'position_id'         => $dto->position_id,
				'position_unknown'    => $dto->position_unknown,
				'faceToFaceMeeting'   => $dto->faceToFaceMeeting,
				'warning'             => $dto->warning,
				'good'                => $dto->good,
				'passive_why'         => $dto->passive_why,
				'passive_why_comment' => $dto->passive_why_comment,
				'warning_why_comment' => $dto->warning_why_comment,
				'status'              => $dto->status,
				'consultant_id'       => $dto->consultant_id
			]);

			$model->saveOrThrow();

			$model->updateManyMiniModels([
				Email::class          => $dto->emails,
				Website::class        => $dto->websites,
				WayOfInforming::class => $dto->wayOfInformings
			]);

			if ($dto->isMain && !$isMainContact) {
				$this->setMainContact($model);
			}

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @param Contact $model
	 *
	 * @return void
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(Contact $model): void
	{
		$model->delete();
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function markAsPassive(Contact $contact, DisableContactDto $dto): void
	{
		if ($contact->isPassive()) {
			return;
		}

		$tx = $this->transactionBeginner->begin();

		try {
			$contact->status = Contact::STATUS_PASSIVE;

			$contact->passive_why         = $dto->passive_why;
			$contact->passive_why_comment = $dto->passive_why_comment;

			$contact->saveOrThrow();

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function markAsActive(Contact $contact): void
	{
		if ($contact->isActive()) {
			return;
		}

		$tx = $this->transactionBeginner->begin();

		try {
			$contact->status = Contact::STATUS_ACTIVE;

			$contact->passive_why         = null;
			$contact->passive_why_comment = null;

			$contact->saveOrThrow();

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function transferToCompany(Contact $contact, TransferContactToCompanyDto $dto): void
	{
		if ($contact->company_id === $dto->company->id) {
			return;
		}

		$clonedContactDto = $this->createContactDtoMapper->fromRecord($contact);

		$clonedContactDto->isMain        = $dto->is_main;
		$clonedContactDto->company_id    = $dto->company->id;
		$clonedContactDto->consultant_id = $dto->consultant->id;
		$clonedContactDto->status        = Contact::STATUS_ACTIVE;

		$createPhonesDtos = ArrayHelper::map($contact->phones, [$this->phoneDtoMapper, 'fromRecord']);

		$tx = $this->transactionBeginner->begin();

		try {
			$this->create($clonedContactDto, $createPhonesDtos);

			if ($dto->disable_contact) {
				$this->markAsPassive($contact, new DisableContactDto([
					'passive_why' => Contact::PASSIVE_WHY_NOT_WORKING_IN_COMPANY
				]));
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}