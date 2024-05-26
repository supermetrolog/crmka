<?php

declare(strict_types=1);

namespace app\usecases\Mailing;

use app\dto\Mailing\CreateMailingDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Notification\Mailing;

class MailingService
{

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateMailingDto $dto): Mailing
	{
		$model = new Mailing();

		$model->channel_id      = $dto->channel_id;
		$model->subject         = $dto->subject;
		$model->message         = $dto->message;
		$model->created_by_type = $dto->created_by_type;
		$model->created_by_id   = $dto->created_by_id;

		$model->saveOrThrow();

		return $model;
	}
}