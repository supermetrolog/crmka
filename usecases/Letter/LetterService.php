<?php

namespace app\usecases\Letter;

use app\dto\Letter\SendLetterDto;
use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\models\letter\CreateLetter;
use app\models\letter\Letter;
use app\services\queue\jobs\SendCustomLetterJob;
use Throwable;
use Yii;

class LetterService
{
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(TransactionBeginnerInterface $transactionBeginner)
	{
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @throws Throwable
	 */
	public function send(SendLetterDto $dto): Letter
	{
		$tx = $this->transactionBeginner->begin();

		// TODO: Refactor me

		try {
			$createLetterModel = new CreateLetter();

			$createLetterModel->create([
				'user_id'         => $dto->user_id,
				'company_id'      => $dto->company_id,
				'sender_email'    => $dto->sender_email,
				'subject'         => $dto->subject,
				'body'            => $dto->body,
				'contacts'        => [
					'emails' => $dto->emails,
					'phones' => $dto->phones,
				],
				'ways'            => $dto->ways,
				'shipping_method' => $dto->shipping_method,
				'type'            => Letter::TYPE_DEFAULT,
				'offers'          => []
			]);

			if ($createLetterModel->letterModel->shipping_method === Letter::SHIPPING_OTHER_METHOD) {
				$tx->commit();

				return $createLetterModel->letterModel;
			}

			Yii::$app->queue->push(
				new SendCustomLetterJob([
					'letter_id'     => $createLetterModel->letterModel->id,
					'user_id'       => $dto->user_id,
					'emails'        => ArrayHelper::map($dto->emails, static fn($email) => ArrayHelper::getValue($email, 'value')),
					'subject'       => $dto->subject,
					'body'          => $dto->body,
					'ways'          => $dto->ways,
					'showSignature' => $dto->show_signature
				])
			);

			$tx->commit();

			return $createLetterModel->letterModel;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}
}
