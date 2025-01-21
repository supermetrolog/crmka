<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectTaskService;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\ObjectChatMember;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use Throwable;

class ObjectHasEquipmentForSaleEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = 'Объект %s - есть оборудование под продажу, нужно обработать.';

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
		$chatMemberModel = $survey->chatMember->model;

		$this->effectTaskService->createTaskForMessage(
			$surveyChatMemberMessage,
			$survey->user,
			$surveyQuestionAnswer,
			$this->getTaskMessage($chatMemberModel)
		);
	}

	public function getTaskMessage(ObjectChatMember $objectChatMember): string
	{
		return sprintf(self::TASK_MESSAGE_TEXT, $objectChatMember->object_id);
	}
}