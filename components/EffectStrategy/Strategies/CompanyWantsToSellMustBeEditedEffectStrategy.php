<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectTaskService;
use app\enum\Effect\EffectKind;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use Exception;
use Throwable;

class CompanyWantsToSellMustBeEditedEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = 'Продажа объекта #%s (%s)';

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
		if ($answer->surveyQuestionAnswer->hasPositiveAnswer()) {
			$chatMember = $survey->chatMember;

			return $chatMember->isObjectChatMember() && $chatMember->objectChatMember->isRentOrSale();
		}

		return false;
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
			$this->getTaskTitle($survey),
			$this->getTaskMessage($survey)
		);
	}

	private function getTaskTitle(Survey $survey): string
	{
		$chatMemberModel = $survey->chatMember->model;
		$company         = $chatMemberModel->company;

		return sprintf(self::TASK_MESSAGE_TEXT, $chatMemberModel->object_id, $company->getShortName());
	}

	/**
	 * @throws Exception
	 */
	private function getTaskMessage(Survey $survey): string
	{
		$surveyQuestionAnswerDescription = $survey->getSurveyQuestionAnswerByEffectKind(EffectKind::COMPANY_WANTS_TO_SELL_MUST_BE_EDITED_DESCRIPTION);

		if ($surveyQuestionAnswerDescription && $surveyQuestionAnswerDescription->hasAnswer()) {
			return $surveyQuestionAnswerDescription->getString();
		}

		return 'Подробности в опроснике';
	}
}