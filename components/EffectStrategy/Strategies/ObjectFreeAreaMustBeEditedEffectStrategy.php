<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectTaskService;
use app\enum\EffectKind;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use Exception;
use Throwable;

class ObjectFreeAreaMustBeEditedEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = 'Объект #%s, свободная площадь в аренду, %s.';

	private CreateEffectTaskService $effectTaskService;

	public function __construct(CreateEffectTaskService $effectTaskService)
	{
		$this->effectTaskService = $effectTaskService;
	}

	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		return $answer->surveyQuestionAnswer->getMaybeBool();
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$this->effectTaskService->createTaskForMessage(
			$surveyChatMemberMessage,
			$survey->user,
			$surveyQuestionAnswer,
			$this->getTaskMessage($survey)
		);
	}

	/**
	 * @throws Exception
	 */
	public function getTaskMessage(Survey $survey): string
	{
		$chatMemberModel = $survey->chatMember->model;

		$surveyQuestionAnswerDescription = $survey->getSurveyQuestionAnswerByEffectKind(EffectKind::OBJECT_FREE_AREA_MUST_BE_EDITED_DESCRIPTION);

		if ($surveyQuestionAnswerDescription) {
			$description = $surveyQuestionAnswerDescription->value;
		} else {
			$description = 'подробности в опроснике';
		}

		return sprintf(self::TASK_MESSAGE_TEXT, $chatMemberModel->object_id, $description);
	}
}