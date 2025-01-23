<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectSystemMessageService;
use app\components\EffectStrategy\Service\CreateEffectTaskService;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\services\ChatMemberSystemMessage\CompanyHasNewRequestSystemMessage;
use Throwable;

class CompanyHasNewRequestEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = 'Новый запрос у %s (#%s), нужно оформить его.';

	private TransactionBeginnerInterface     $transactionBeginner;
	private CreateEffectTaskService          $effectTaskService;
	private CreateEffectSystemMessageService $effectSystemMessageService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		CreateEffectTaskService $effectTaskService,
		CreateEffectSystemMessageService $effectSystemMessageService
	)
	{
		$this->transactionBeginner        = $transactionBeginner;
		$this->effectTaskService          = $effectTaskService;
		$this->effectSystemMessageService = $effectSystemMessageService;
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
		$chatMember      = $survey->chatMember;
		$chatMemberModel = $chatMember->model;

		$tx = $this->transactionBeginner->begin();

		try {
			if ($chatMember->model_type !== Company::getMorphClass()) {
				$chatMemberModel = $chatMember->model->company;
				$chatMember      = $chatMember->model->company->chatMember;

				$this->sendSystemMessageIntoCompany($chatMember, $survey);
			}

			$this->effectTaskService->createTaskForMessage($surveyChatMemberMessage, $survey->user, $surveyQuestionAnswer, $this->getTaskMessage($chatMemberModel));

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function sendSystemMessageIntoCompany(ChatMember $chatMember, Survey $survey): void
	{
		$message = CompanyHasNewRequestSystemMessage::create()
		                                            ->setSurveyId($survey->id)
		                                            ->toMessage();

		$this->effectSystemMessageService->createSystemMessage($chatMember, $survey, $message);
	}

	protected function getTaskMessage(Company $company): string
	{
		return sprintf(self::TASK_MESSAGE_TEXT, $company->getFullName(), $company->id);
	}
}