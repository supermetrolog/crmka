<?php

declare(strict_types=1);

namespace app\usecases\Letter;

use app\dto\Letter\LetterContactEventDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\letter\LetterContactEvent;
use Throwable;
use yii\db\StaleObjectException;

class LetterContactEventService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(LetterContactEventDto $dto): LetterContactEvent
	{
		$action = new LetterContactEvent([
			'letter_contact_id' => $dto->letterContact->id,
			'event_type'        => $dto->eventType,
			'ip'                => $dto->ip,
			'user_agent'        => $dto->userAgent
		]);

		$action->saveOrThrow();

		return $action;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(LetterContactEvent $model): void
	{
		$model->delete();
	}
}