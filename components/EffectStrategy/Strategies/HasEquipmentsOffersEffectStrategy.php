<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectTaskService;
use app\enum\EffectKind;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use Throwable;
use yii\base\Exception;

class HasEquipmentsOffersEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = '%s (#%s) хотят продать оборудование';

	private CreateEffectTaskService $effectTaskService;

	public function __construct(CreateEffectTaskService $effectTaskService)
	{
		$this->effectTaskService = $effectTaskService;
	}

	/**
	 * @throws Exception
	 */
	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		return $answer->surveyQuestionAnswer->hasPositiveAnswer();
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$this->createTask($survey, $surveyQuestionAnswer, $surveyChatMemberMessage);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function createTask(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$this->effectTaskService->createTaskForMessage(
			$surveyChatMemberMessage,
			$survey->user,
			$surveyQuestionAnswer,
			$this->getTaskTitle($survey),
			$this->getTaskMessage($survey)
		);
	}

	private function getTaskTitle(Survey $survey): string
	{
		/** @var Company $company */
		$company = $survey->chatMember->model;

		return sprintf(
			self::TASK_MESSAGE_TEXT,
			$company->getShortName(),
			$company->id
		);
	}

	/**
	 * @throws \Exception
	 */
	private function getTaskMessage(Survey $survey): string
	{
		$surveyQuestionAnswerDescription = $survey->getSurveyQuestionAnswerByEffectKind(EffectKind::HAS_EQUIPMENTS_OFFERS_DESCRIPTION);

		if ($surveyQuestionAnswerDescription && $surveyQuestionAnswerDescription->hasAnswer()) {
			return $surveyQuestionAnswerDescription->getString();
		}

		return 'Подробности в опроснике';
	}
}