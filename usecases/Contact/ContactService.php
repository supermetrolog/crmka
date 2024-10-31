<?php

declare(strict_types=1);

namespace app\usecases\Contact;

use app\dto\Contact\CreateContactDto;
use app\dto\Contact\UpdateContactDto;
use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ActiveQuery\ContactQuery;
use app\models\Contact;
use app\models\miniModels\Email;
use app\models\miniModels\Phone;
use app\models\miniModels\WayOfInforming;
use app\models\miniModels\Website;
use Throwable;
use yii\db\StaleObjectException;

class ContactService
{
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @param CreateContactDto $dto
	 *
	 * @return Contact
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CreateContactDto $dto): Contact
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model = new Contact([
				'company_id'          => $dto->company_id,
				'first_name'          => $dto->first_name,
				'middle_name'         => $dto->middle_name,
				'last_name'           => $dto->last_name,
				'position'            => $dto->position,
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
				Phone::class          => ArrayHelper::merge($dto->phones, $dto->invalidPhones),
				Website::class        => $dto->websites,
				WayOfInforming::class => $dto->wayOfInformings
			]);

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
			/** @var ContactQuery $contactQuery */
			$contactQuery = $model->getRelatedContacts();

			/** @var Contact $preventMainContact */
			$preventMainContact = $contactQuery->main()->one();

			if ($preventMainContact !== null) {
				$preventMainContact->isMain = null;
				$preventMainContact->saveOrThrow();
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
				'position'            => $dto->position,
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
				Phone::class          => ArrayHelper::merge($dto->phones, $dto->invalidPhones),
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
}