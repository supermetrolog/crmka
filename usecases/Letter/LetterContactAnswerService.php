<?php

declare(strict_types=1);

namespace app\usecases\Letter;

use app\dto\LetterContactAnswer\CreateLetterContactAnswerDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\LetterContactAnswer;
use Throwable;
use yii\db\StaleObjectException;

class LetterContactAnswerService
{
	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CreateLetterContactAnswerDto $dto): LetterContactAnswer
	{
		$action = new LetterContactAnswer([
			'letter_contact_id'  => $dto->letterContact->id,
			'marked_by_id'       => $dto->markedBy->id,
			'type'               => $dto->type,
			'comment'            => $dto->comment,
			'related_message_id' => $dto->related_message_id,
			'marked_at'          => DateTimeHelper::nowf()
		]);

		$action->saveOrThrow();

		return $action;
	}

	public function update(LetterContactAnswer $model): void
	{
		// TODO: Implement update() method.
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(LetterContactAnswer $model): void
	{
		$model->delete();
	}
}