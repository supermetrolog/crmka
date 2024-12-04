<?php

namespace app\components\EffectStrategy\Strategies;

use app\builders\Task\TaskBuilderFactory;
use app\components\EffectStrategy\AbstractEffectStrategy;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\ObjectChatMember;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\models\User;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use yii\base\Exception;

class CompanyWantsToSellEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = '%s (#%s) хочет продать объект, нужно создать или обновить предложение.';

	protected ChatMemberMessageService $chatMemberMessageService;
	protected TaskBuilderFactory       $taskBuilderFactory;

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
		return $answer->surveyQuestionAnswer->getMaybeBool() && $survey->chatMember->model_type === ObjectChatMember::getMorphClass();
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$company = $survey->chatMember->model->company;

		$this->createTaskForMessage($surveyChatMemberMessage, $survey->user, $company);
	}

	public function getTaskMessage(Company $company): string
	{
		return sprintf(self::TASK_MESSAGE_TEXT, $company->getFullName(), $company->id);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws Exception
	 */
	protected function createTaskForMessage(ChatMemberMessage $message, User $user, Company $company): void
	{
		$dto = $this->taskBuilderFactory
			->createEffectBuilder()
			->setMessage($this->getTaskMessage($company))
			->setCreatedBy($user)
			->build();

		$this->chatMemberMessageService->createTask($message, $dto);
	}
}