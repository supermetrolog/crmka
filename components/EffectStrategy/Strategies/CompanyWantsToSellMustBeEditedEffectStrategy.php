<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectTaskService;
use app\enum\EffectKind;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\ObjectChatMember;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use Exception;
use Throwable;

class CompanyWantsToSellMustBeEditedEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = '%s (#%s) хочет продать объект #%s, %s.';

	private CreateEffectTaskService $effectTaskService;

	public function __construct(CreateEffectTaskService $effectTaskService)
	{
		$this->effectTaskService = $effectTaskService;
	}

	/**
	 * @throws \yii\base\Exception
	 */
	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		return $answer->surveyQuestionAnswer->hasPositiveAnswer() && $survey->chatMember->model_type === ObjectChatMember::getMorphClass();
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
		$company = $survey->chatMember->model->company;

		$surveyQuestionAnswerDescription = $survey->getSurveyQuestionAnswerByEffectKind(EffectKind::COMPANY_WANTS_TO_SELL_MUST_BE_EDITED_DESCRIPTION);

		if ($surveyQuestionAnswerDescription) {
			$description = $surveyQuestionAnswerDescription->getString() ?? 'подробности в опроснике';
		} else {
			$description = 'подробности в опроснике';
		}


		return sprintf(
			self::TASK_MESSAGE_TEXT,
			$company->getFullName(),
			$company->id,
			$survey->chatMember->model->object_id,
			$description
		);
	}
}