<?php

declare(strict_types=1);

namespace app\usecases\Phone;

use app\dto\Phone\PhoneDto;
use app\enum\Phone\PhoneStatusEnum;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Contact;
use app\models\miniModels\Phone;
use InvalidArgumentException;
use Throwable;
use yii\db\StaleObjectException;

class PhoneService
{

	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function createForContact(Contact $contact, PhoneDto $dto): Phone
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model = new Phone([
				'contact_id'   => $contact->id,
				'status'       => PhoneStatusEnum::ACTIVE,
				'type'         => $dto->type,
				'country_code' => $dto->countryCode,
				'phone'        => $dto->phone,
				'exten'        => $dto->exten,
				'comment'      => $dto->comment
			]);

			$model->saveOrThrow();

			if ($dto->isMain) {
				$this->markAsMain($model);
			}

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function update(Phone $model, PhoneDto $dto): Phone
	{
		$tx = $this->transactionBeginner->begin();

		try {
			if ($dto->isMain && !$model->isMainPhone()) {
				$this->markAsMain($model);
			}

			$model->load([
				'type'    => $dto->type,
				'phone'   => $dto->phone,
				'exten'   => $dto->exten,
				'comment' => $dto->comment
			]);

			$model->saveOrThrow();

			$tx->commit();


			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function markAsMain(Phone $phone): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			Phone::updateAll(['isMain' => null], ['contact_id' => $phone->contact_id]);

			$phone->isMain = 1;
			$phone->saveOrThrow();

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 */
	private function setStatus(Phone $phone, string $status): void
	{
		if (!PhoneStatusEnum::isValid($status)) {
			throw new InvalidArgumentException("Invalid phone status: $status");
		}

		if ($phone->status === $status) {
			return;
		}

		$phone->status = $status;

		$phone->saveOrThrow();
	}

	/**
	 * @throws SaveModelException
	 */
	public function markAsPassive(Phone $phone): void
	{
		$this->setStatus($phone, PhoneStatusEnum::PASSIVE);
	}

	/**
	 * @throws SaveModelException
	 */
	public function markAsActive(Phone $phone): void
	{
		$this->setStatus($phone, PhoneStatusEnum::ACTIVE);
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(Phone $phone): void
	{
		$phone->delete();
	}
}