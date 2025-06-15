<?php

declare(strict_types=1);

namespace app\usecases\Company;

use app\dto\Company\CompanyPinnedMessageDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\CompanyPinnedMessage;
use Throwable;
use yii\db\StaleObjectException;

class CompanyPinnedMessageService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CompanyPinnedMessageDto $dto): CompanyPinnedMessage
	{
		$model = new CompanyPinnedMessage([
			'company_id'             => $dto->company->id,
			'chat_member_message_id' => $dto->message->id,
			'created_by_id'          => $dto->user->id,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(CompanyPinnedMessage $model): void
	{
		$model->delete();
	}
}