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
use app\services\ChatMemberSystemMessage\CompanyWantsToBuyOrBuildSystemMessage;
use Throwable;
use yii\base\Exception;

class CompanyWantsToBuyOrBuildEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = '%s (#%s) - хотят купить или построить объект, нужно предложить им.';

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
		$chatMember      = $survey->chatMember;
		$chatMemberModel = $chatMember->model;

		$tx = $this->transactionBeginner->begin();

		try {
			if ($chatMember->model_type !== Company::getMorphClass()) {
				$chatMemberModel   = $chatMemberModel->company;
				$companyChatMember = $chatMemberModel->chatMember;

				$this->sendSystemMessageIntoCompany($companyChatMember, $survey);
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
		$message = CompanyWantsToBuyOrBuildSystemMessage::create()
		                                                ->setSurveyId($survey->id)
		                                                ->toMessage();

		$this->effectSystemMessageService->createSystemMessage($chatMember, $survey, $message);
	}

	public function getTaskMessage(Company $company): string
	{
		return sprintf(self::TASK_MESSAGE_TEXT, $company->getFullName(), $company->id);
	}
}
