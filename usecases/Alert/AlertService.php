<?php

declare(strict_types=1);

namespace app\usecases\Alert;

use app\dto\Alert\UpdateAlertDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Alert;
use Throwable;
use yii\db\StaleObjectException;

class AlertService
{

	/**
	 * @throws SaveModelException
	 */
	public function update(Alert $alert, UpdateAlertDto $dto): Alert
	{
		$alert->load([
			'user_id' => $dto->user->id,
			'message' => $dto->message,
		]);

		$alert->saveOrThrow();

		return $alert;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Alert $task): void
	{
		$task->delete();
	}
}