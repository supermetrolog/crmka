<?php

declare(strict_types=1);

namespace app\usecases\Survey;

use app\components\EventManager;
use app\dto\Survey\CreateSurveyDto;
use app\dto\Survey\UpdateSurveyDto;
use app\dto\SurveyQuestionAnswer\CreateSurveyQuestionAnswerDto;
use app\events\Survey\CreateSurveyEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Question;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\usecases\SurveyQuestionAnswer\SurveyQuestionAnswerService;
use Throwable;
use yii\db\ActiveQuery;
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
	 * @throws ModelNotFoundException
	 */
	public function getByIdWithRelationsOrThrow(int $id): Survey
	{
		return Survey::find()
		             ->byId($id)
		             ->with(['tasks.user.userProfile',
		                     'tasks.tags',
		                     'tasks.createdByUser.userProfile',
		                     'tasks.observers.user.userProfile',
		                     'tasks.targetUserObserver'])
		             ->oneOrThrow();
	}

	/**
	 * @return Question[]
	 */
	public function getQuestionsWithAnswersBySurveyId(int $surveyId): array
	{
		return Question::find()
		               ->joinWith([
			               'answers.surveyQuestionAnswer' => function (ActiveQuery $query) use ($surveyId) {
				               $query->where([SurveyQuestionAnswer::field('survey_id') => $surveyId]);
				               $query->with(['tasks.user.userProfile',
				                             'tasks.tags',
				                             'tasks.createdByUser.userProfile',
				                             'tasks.observers.user.userProfile',
				                             'tasks.targetUserObserver']);
			               }
		               ])
		               ->all();
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
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Survey $model): void
	{
		$model->delete();
	}
}