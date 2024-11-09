<?php

declare(strict_types=1);

namespace app\usecases\Survey;

use app\components\EventManager;
use app\dto\Survey\CreateSurveyDto;
use app\dto\Survey\UpdateSurveyDto;
use app\dto\SurveyQuestionAnswer\CreateSurveyQuestionAnswerDto;
use app\events\Survey\CreateSurveyEvent;
use app\events\Survey\SurveyRequestsNoLongerRelevantEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\QuestionAnswer;
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
	 * @throws SaveModelException
	 */
	public function createWithSurveyQuestionAnswer(CreateSurveyDto $dto, array $answerDtos = []): Survey
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$survey = $this->create($dto);

			$event = new CreateSurveyEvent($survey);
			$this->eventManager->trigger($event);

			foreach ($answerDtos as $answerDto) {
				$this->createSurveyQuestionAnswer($survey, $answerDto);
			}

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

		$answer = $this->surveyQuestionAnswerService->create($dto);

		$this->trackEvents($answer);

		return $answer;
	}

	private function trackEvents(SurveyQuestionAnswer $answer): void
	{
		if ($answer->question_answer_id === QuestionAnswer::ANSWER_ID_WITH_DISABLE_REQUESTS_EVENT) {
			$eventShouldBeTriggered = json_decode($answer->value);

			if ($eventShouldBeTriggered) {
				$event = new SurveyRequestsNoLongerRelevantEvent($answer->survey_id);
				$this->eventManager->trigger($event);
			}
		}
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
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Survey $model): void
	{
		$model->delete();
	}
}