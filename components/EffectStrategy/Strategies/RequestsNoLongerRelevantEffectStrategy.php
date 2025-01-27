<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectSystemMessageService;
use app\components\EffectStrategy\Service\CreateEffectTaskService;
use app\helpers\ArrayHelper;
use app\helpers\StringHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\Company;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\services\ChatMemberSystemMessage\RequestsNoLongerRelevantChatMemberSystemMessage;
use Throwable;
use yii\base\Exception;

class RequestsNoLongerRelevantEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = '%s (#%s) - устарели запросы %s, необходимо отправить их в пассив.';

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
		$jsonData = $answer->surveyQuestionAnswer->getJSON();

		return isset($jsonData['archived']) && ArrayHelper::isArray($jsonData['archived']) && ArrayHelper::notEmpty($jsonData['archived']);
	}

	/**
	 * @param int[] $requestIds
	 */
	protected function getTaskMessage(Company $company, array $requestIds): string
	{
		return sprintf(
			self::TASK_MESSAGE_TEXT,
			$company->getFullName(),
			$company->id,
			StringHelper::join(
				StringHelper::SPACED_COMMA,
				...ArrayHelper::map($requestIds, static function (int $id): string {
				return "#$id";
			})
			)
		);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$archivedRequests = $surveyQuestionAnswer->getJSON()['archived'];

			$chatMember      = $survey->chatMember;
			$chatMemberModel = $chatMember->model;

			if ($chatMember->model_type !== Company::getMorphClass()) {
				$chatMemberModel = $chatMemberModel->company;
				$chatMember      = $chatMemberModel->chatMember;

				$this->sendSystemMessageIntoCompany($chatMember, $survey, $archivedRequests);
			}

			$this->effectTaskService->createTaskForMessage(
				$surveyChatMemberMessage,
				$survey->user,
				$surveyQuestionAnswer,
				$this->getTaskMessage($chatMemberModel, $archivedRequests)
			);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @param int[] $archivedRequests
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function sendSystemMessageIntoCompany(ChatMember $chatMember, Survey $survey, array $archivedRequests): void
	{
		$message = RequestsNoLongerRelevantChatMemberSystemMessage::create()->setRequestIds($archivedRequests)->toMessage();

		$this->effectSystemMessageService->createSystemMessage($chatMember, $survey, $message);
	}
}