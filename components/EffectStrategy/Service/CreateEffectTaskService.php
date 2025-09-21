<?php

declare(strict_types=1);

namespace app\components\EffectStrategy\Service;

use app\builders\Task\TaskBuilderFactory;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\SurveyQuestionAnswer;
use app\models\Task;
use app\models\User\User;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;

class CreateEffectTaskService
{
	private TaskBuilderFactory       $taskBuilderFactory;
	private ChatMemberMessageService $chatMemberMessageService;

	public function __construct(TaskBuilderFactory $taskBuilderFactory, ChatMemberMessageService $chatMemberMessageService)
	{
		$this->taskBuilderFactory       = $taskBuilderFactory;
		$this->chatMemberMessageService = $chatMemberMessageService;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function createTaskForMessage(ChatMemberMessage $message, User $user, SurveyQuestionAnswer $surveyQuestionAnswer, string $title, ?string $text = null): Task
	{
		$dto = $this->taskBuilderFactory
			->createEffectBuilder()
			->setTitle($title)
			->setMessage($text)
			->setCreatedBy($user)
			->setSurveyQuestionAnswerId($surveyQuestionAnswer->id)
			->build();

		return $this->chatMemberMessageService->createTask($message, $dto);
	}
}