<?php

declare(strict_types=1);

namespace app\usecases\Question;

use app\dto\Question\CreateQuestionDto;
use app\dto\Question\UpdateQuestionDto;
use app\dto\QuestionAnswer\CreateQuestionAnswerDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Question;
use app\models\QuestionAnswer;
use app\usecases\QuestionAnswer\QuestionAnswerService;
use Throwable;
use yii\db\StaleObjectException;

class QuestionService
{
	private TransactionBeginnerInterface $transactionBeginner;
	protected QuestionAnswerService      $questionAnswerService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		QuestionAnswerService $questionAnswerService
	)
	{
		$this->transactionBeginner   = $transactionBeginner;
		$this->questionAnswerService = $questionAnswerService;
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateQuestionDto $dto): Question
	{
		$model = new Question([
			'text' => $dto->text,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function createWithQuestionAnswer(CreateQuestionDto $dto, CreateQuestionAnswerDto $answerDto): Question
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model = $this->create($dto);
			$this->createQuestionAnswer($model, $answerDto);

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws \Exception
	 * @throws Throwable
	 */
	public function createQuestionAnswer(Question $question, CreateQuestionAnswerDto $dto): QuestionAnswer
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$dto->question_id = $question->id;

			$model = $this->questionAnswerService->create($dto);

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(Question $model, UpdateQuestionDto $dto): Question
	{
		$model->load([
			'text' => $dto->text,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Question $model): void
	{
		$model->delete();
	}
}