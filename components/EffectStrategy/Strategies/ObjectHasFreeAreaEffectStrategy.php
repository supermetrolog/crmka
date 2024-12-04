<?php

namespace app\components\EffectStrategy\Strategies;

use app\builders\Task\TaskBuilderFactory;
use app\components\EffectStrategy\AbstractEffectStrategy;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\ObjectChatMember;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\models\User;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use yii\base\Exception;

class ObjectHasFreeAreaEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = 'Объект %s - есть свободная площадь в аренду, нужно обработать.';

	private ChatMemberMessageService $chatMemberMessageService;
	private TaskBuilderFactory       $taskBuilderFactory;

	public function __construct(
		ChatMemberMessageService $chatMemberMessageService,
		TaskBuilderFactory $taskBuilderFactory
	)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
		$this->taskBuilderFactory       = $taskBuilderFactory;
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

		$this->createTaskForMessage($surveyChatMemberMessage, $survey->user, $chatMemberModel);
	}

	public function getTaskMessage(ObjectChatMember $objectChatMember): string
	{
		return sprintf(self::TASK_MESSAGE_TEXT, $objectChatMember->object_id);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws Exception
	 */
	protected function createTaskForMessage(ChatMemberMessage $message, User $user, ObjectChatMember $objectChatMember): void
	{
		$dto = $this->taskBuilderFactory
			->createEffectBuilder()
			->setMessage($this->getTaskMessage($objectChatMember))
			->setCreatedBy($user)
			->build();

		$this->chatMemberMessageService->createTask($message, $dto);
	}
}