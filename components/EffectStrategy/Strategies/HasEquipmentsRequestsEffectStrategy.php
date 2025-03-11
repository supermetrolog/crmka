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

class HasEquipmentsRequestsEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = '%s (#%s) хотят купить обрудование, %s.';

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
		$taskMessage = $this->getTaskMessage($survey);


		$this->effectTaskService->createTaskForMessage(
			$surveyChatMemberMessage,
			$survey->user,
			$surveyQuestionAnswer,
			$taskMessage
		);
	}

	/**
	 * @throws Exception
	 * @throws \Exception
	 */
	public function getTaskMessage(Survey $survey): string
	{
		/** @var Company $company */
		$company = $survey->chatMember->model;

		$surveyQuestionAnswerDescription = $survey->getSurveyQuestionAnswerByEffectKind(EffectKind::HAS_EQUIPMENTS_REQUESTS_DESCRIPTION);

		$description = 'подробности в опроснике';

		if ($surveyQuestionAnswerDescription && $surveyQuestionAnswerDescription->hasAnswer()) {
			$description = $surveyQuestionAnswerDescription->getString();
		}

		return sprintf(
			self::TASK_MESSAGE_TEXT,
			$company->getShortName(),
			$company->id,
			$description
		);
	}
}