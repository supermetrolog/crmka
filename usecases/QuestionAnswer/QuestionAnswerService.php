<?php

declare(strict_types=1);

namespace app\usecases\QuestionAnswer;

use app\dto\QuestionAnswer\CreateQuestionAnswerDto;
use app\dto\QuestionAnswer\UpdateQuestionAnswerDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\QuestionAnswer;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

class QuestionAnswerService
{
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(TransactionBeginnerInterface $transactionBeginner)
	{
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 */
	public function create(CreateQuestionAnswerDto $dto): QuestionAnswer
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model = new QuestionAnswer([
				'question_id' => $dto->question_id,
				'field_id'    => $dto->field_id,
				'category'    => $dto->category,
				'value'       => $dto->value,
				'message'     => $dto->message
			]);

			$model->saveOrThrow();

			$model->linkManyToManyRelations('effects', $dto->effectIds);

			$tx->commit();

			return $model;
		} catch (Throwable $e) {
			$tx->rollBack();
			throw $e;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 */
	public function update(QuestionAnswer $model, UpdateQuestionAnswerDto $dto): QuestionAnswer
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model->load([
				'question_id' => $dto->question_id,
				'field_id'    => $dto->field_id,
				'category'    => $dto->category,
				'value'       => $dto->value,
				'message'     => $dto->message
			]);

			$model->saveOrThrow();

			$model->updateManyToManyRelations('effects', $dto->effectIds);

			$tx->commit();

			return $model;
		} catch (Throwable $e) {
			$tx->rollBack();
			throw $e;
		}
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(QuestionAnswer $model): void
	{
		$model->delete();
	}
}