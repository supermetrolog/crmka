<?php

declare(strict_types=1);

namespace app\usecases\Survey;

use app\components\EventManager;
use app\dto\Survey\CreateSurveyDto;
use app\dto\Survey\UpdateSurveyDto;
use app\dto\SurveyQuestionAnswer\CreateSurveyQuestionAnswerDto;
use app\dto\SurveyQuestionAnswer\UpdateSurveyQuestionAnswerDto;
use app\events\Survey\CreateSurveyEvent;
use app\events\Survey\UpdateSurveyEvent;
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
	private EventManager                  $eventManager;
	protected SurveyQuestionAnswerService $surveyQuestionAnswerService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		SurveyQuestionAnswerService $surveyQuestionAnswerService,
		EventManager $eventManager
	)
	{
		$this->transactionBeginner         = $transactionBeginner;
		$this->surveyQuestionAnswerService = $surveyQuestionAnswerService;
		$this->eventManager                = $eventManager;
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateSurveyDto $dto): Survey
	{
		$model = new Survey([
			'user_id'        => $dto->user->id,
			'contact_id'     => $dto->contact->id,
			'chat_member_id' => $dto->chatMember->id,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @param CreateSurveyQuestionAnswerDto[] $answerDtos
	 *
	 * @throws SaveModelException|Throwable
	 */
	public function createWithSurveyQuestionAnswer(CreateSurveyDto $dto, array $answerDtos = []): Survey
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$survey = $this->create($dto);

			$event = new CreateSurveyEvent($survey);

			foreach ($answerDtos as $answerDto) {
				$this->createSurveyQuestionAnswer($survey, $answerDto);
			}

			$this->eventManager->trigger($event);
			$tx->commit();

			return $survey;
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
		$dto->survey_id = $survey->id;

		return $this->surveyQuestionAnswerService->create($dto);
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(Survey $model, UpdateSurveyDto $dto): Survey
	{
		$model->load([
			'user_id'        => $dto->user->id,
			'contact_id'     => $dto->contact->id,
			'chat_member_id' => $dto->chatMember->id,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function updateWithQuestionAnswer(Survey $survey, array $answerDtos = []): Survey
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$event = new UpdateSurveyEvent($survey);

			foreach ($answerDtos as $answerDto) {
				$this->updateOrCreateSurveyQuestionAnswer($survey, $answerDto);
			}

			$survey->saveOrThrow();

			$this->eventManager->trigger($event);
			$tx->commit();

			return $survey;
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
	public function updateOrCreateSurveyQuestionAnswer(Survey $survey, CreateSurveyQuestionAnswerDto $dto): SurveyQuestionAnswer
	{
		$surveyQuestionAnswer = $this->surveyQuestionAnswerService->getBySurveyIdAndQuestionAnswerId($survey->id, $dto->question_answer_id);

		if ($surveyQuestionAnswer) {
			return $this->surveyQuestionAnswerService->update(
				$surveyQuestionAnswer,
				new UpdateSurveyQuestionAnswerDto([
					'survey_id'          => $survey->id,
					'question_answer_id' => $dto->question_answer_id,
					'value'              => $dto->value
				])
			);
		}

		$dto->survey_id = $survey->id;

		return $this->createSurveyQuestionAnswer($survey, $dto);
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