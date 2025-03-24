<?php

declare(strict_types=1);

namespace app\usecases\Survey;

use app\components\EventManager;
use app\dto\Call\CreateCallDto;
use app\dto\Media\CreateMediaDto;
use app\dto\Relation\CreateRelationDto;
use app\dto\Survey\CreateSurveyDto;
use app\dto\Survey\UpdateSurveyDto;
use app\dto\SurveyQuestionAnswer\CreateSurveyQuestionAnswerDto;
use app\dto\SurveyQuestionAnswer\UpdateSurveyQuestionAnswerDto;
use app\events\Survey\CreateSurveyEvent;
use app\events\Survey\UpdateSurveyEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Call;
use app\models\Relation;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\usecases\Call\CreateCallService;
use app\usecases\ChatMember\ChatMemberService;
use app\usecases\Relation\RelationService;
use app\usecases\SurveyQuestionAnswer\SurveyQuestionAnswerService;
use Throwable;
use yii\base\ErrorException;
use yii\db\StaleObjectException;

class SurveyService
{
	private TransactionBeginnerInterface  $transactionBeginner;
	private EventManager                  $eventManager;
	protected SurveyQuestionAnswerService $surveyQuestionAnswerService;
	private CreateCallService             $createCallService;
	private RelationService               $relationService;
	private ChatMemberService             $chatMemberService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		SurveyQuestionAnswerService $surveyQuestionAnswerService,
		EventManager $eventManager,
		CreateCallService $createCallService,
		RelationService $relationService,
		ChatMemberService $chatMemberService
	)
	{
		$this->transactionBeginner         = $transactionBeginner;
		$this->surveyQuestionAnswerService = $surveyQuestionAnswerService;
		$this->eventManager                = $eventManager;
		$this->createCallService           = $createCallService;
		$this->relationService             = $relationService;
		$this->chatMemberService           = $chatMemberService;
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateSurveyDto $dto): Survey
	{
		$model = new Survey([
			'user_id'           => $dto->user->id,
			'contact_id'        => $dto->contact->id,
			'chat_member_id'    => $dto->chatMember->id,
			'related_survey_id' => $dto->related_survey_id
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @param CreateCallDto[]                 $callDtos
	 * @param CreateSurveyQuestionAnswerDto[] $answerDtos
	 * @param array<CreateMediaDto[]>         $mediaDtosMap
	 *
	 * @throws SaveModelException|Throwable
	 */
	public function createWithSurveyQuestionAnswer(CreateSurveyDto $dto, array $answerDtos = [], array $callDtos = [], array $mediaDtosMap = []): Survey
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$survey = $this->create($dto);

			$event = new CreateSurveyEvent($survey);

			foreach ($answerDtos as $answerDto) {
				$this->createSurveyQuestionAnswer($survey, $answerDto, $mediaDtosMap[$answerDto->question_answer_id] ?? []);
			}

			foreach ($callDtos as $callDto) {
				$this->createCall($survey, $callDto);
			}

			foreach ($dto->calls as $call) {
				$this->linkCall($survey, $call);
				$this->chatMemberService->linkCall($survey->chatMember, $call);
			}

			$this->eventManager->trigger($event);
			$tx->commit();

			$survey->refresh();

			return $survey;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @param CreateMediaDto[] $mediaDtos
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 */
	public function createSurveyQuestionAnswer(Survey $survey, CreateSurveyQuestionAnswerDto $dto, array $mediaDtos = []): SurveyQuestionAnswer
	{
		$dto->survey_id = $survey->id;

		return $this->surveyQuestionAnswerService->create($dto, $mediaDtos);
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(Survey $model, UpdateSurveyDto $dto): Survey
	{
		$model->load([
			'user_id'           => $dto->user->id,
			'contact_id'        => $dto->contact->id,
			'chat_member_id'    => $dto->chatMember->id,
			'related_survey_id' => $dto->related_survey_id
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @param array<CreateMediaDto[]> $mediaDtosMap
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function updateWithQuestionAnswer(Survey $survey, array $answerDtos = [], array $mediaDtosMap = []): Survey
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$event = new UpdateSurveyEvent($survey);

			foreach ($answerDtos as $answerDto) {
				$this->updateOrCreateSurveyQuestionAnswer(
					$survey,
					$answerDto,
					$mediaDtosMap[$answerDto->question_answer_id] ?? []
				);
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
	 * @param CreateMediaDto[] $mediaDtos
	 *
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ErrorException
	 */
	public function updateOrCreateSurveyQuestionAnswer(Survey $survey, CreateSurveyQuestionAnswerDto $dto, array $mediaDtos = []): SurveyQuestionAnswer
	{
		$surveyQuestionAnswer = $this->surveyQuestionAnswerService->getBySurveyIdAndQuestionAnswerId($survey->id, $dto->question_answer_id);

		if ($surveyQuestionAnswer) {
			$updateDto = new UpdateSurveyQuestionAnswerDto([
				'survey_id'          => $survey->id,
				'question_answer_id' => $dto->question_answer_id,
				'value'              => $dto->value
			]);

			return $this->surveyQuestionAnswerService->update($surveyQuestionAnswer, $updateDto, $mediaDtos);
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

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function createCall(Survey $survey, CreateCallDto $callDto): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$call = $this->createCallService->create($callDto);

			$this->linkCall($survey, $call);
			$this->chatMemberService->linkCall($survey->chatMember, $call);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 */
	public function linkCall(Survey $survey, Call $call): void
	{
		$this->linkRelation($survey, $call::getMorphClass(), $call->id);
	}

	/**
	 * @throws SaveModelException
	 */
	public function linkRelation(Survey $survey, string $relationType, $relationId): Relation
	{
		return $this->relationService->create(
			new CreateRelationDto([
				'first_type'  => $survey::getMorphClass(),
				'first_id'    => $survey->id,
				'second_type' => $relationType,
				'second_id'   => $relationId
			])
		);
	}
}