<?php

declare(strict_types=1);

namespace app\usecases\Survey;

use app\dto\Survey\CreateSurveyDto;
use app\dto\Survey\UpdateSurveyDto;
use app\dto\SurveyQuestionAnswer\CreateSurveyQuestionAnswerDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\usecases\SurveyQuestionAnswer\SurveyQuestionAnswerService;
use Throwable;
use yii\db\StaleObjectException;

class SurveyService
{
	private TransactionBeginnerInterface  $transactionBeginner;
	protected SurveyQuestionAnswerService $surveyQuestionAnswerService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		SurveyQuestionAnswerService $surveyQuestionAnswerService
	)
	{
		$this->transactionBeginner         = $transactionBeginner;
		$this->surveyQuestionAnswerService = $surveyQuestionAnswerService;
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateSurveyDto $dto): Survey
	{
		$model = new Survey([
			'user_id'    => $dto->user->id,
			'contact_id' => $dto->contact->id,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function createWithSurveyQuestionAnswer(CreateSurveyDto $dto, CreateSurveyQuestionAnswerDto $answerDto): Survey
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$question = $this->create($dto);
			$this->createSurveyQuestionAnswer($question, $answerDto);

			$tx->commit();

			return $question;
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
	public function createSurveyQuestionAnswer(Survey $survey, CreateSurveyQuestionAnswerDto $dto): SurveyQuestionAnswer
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$dto->survey_id = $survey->id;

			$model = $this->surveyQuestionAnswerService->create($dto);

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
	public function update(Survey $model, UpdateSurveyDto $dto): Survey
	{
		$model->load([
			'user_id'    => $dto->user->id,
			'contact_id' => $dto->contact->id,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Survey $model): void
	{
		$model->delete();
	}
}