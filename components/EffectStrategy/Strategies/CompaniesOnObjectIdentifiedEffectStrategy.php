<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectSystemMessageService;
use app\components\EffectStrategy\Service\CreateEffectTaskService;
use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\ObjectChatMember;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\repositories\ChatMemberRepository;
use app\services\ChatMemberSystemMessage\CompanyOnObjectChatMemberSystemMessage;
use Throwable;
use yii\base\Exception;

class CompaniesOnObjectIdentifiedEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT_WITH_NEW_COMPANIES    = 'Объект #%s, выявлено %d арендатор(а), %d новых. ';
	private const TASK_MESSAGE_TEXT_WITHOUT_NEW_COMPANIES = 'Объект #%s, выявлено %d арендатор(а)';

	private CreateEffectSystemMessageService $effectSystemMessageService;
	private CreateEffectTaskService          $effectTaskService;
	private TransactionBeginnerInterface     $transactionBeginner;
	private ChatMemberRepository             $chatMemberRepository;

	public function __construct(
		CreateEffectSystemMessageService $effectSystemMessageService,
		CreateEffectTaskService $effectTaskService,
		TransactionBeginnerInterface $transactionBeginner,
		ChatMemberRepository $chatMemberRepository
	)
	{
		$this->effectSystemMessageService = $effectSystemMessageService;
		$this->effectTaskService          = $effectTaskService;
		$this->transactionBeginner        = $transactionBeginner;
		$this->chatMemberRepository       = $chatMemberRepository;
	}

	/**
	 * @throws Exception
	 */
	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		if ($answer->surveyQuestionAnswer->hasAnswer()) {
			$jsonData = $answer->surveyQuestionAnswer->getJSON();

			return ArrayHelper::isArray($jsonData) && ArrayHelper::notEmpty($jsonData) && $survey->chatMember->isObjectChatMember() && $survey->chatMember->model->isRentOrSale();
		}

		return false;
	}

	/**
	 * @throws Exception
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$companiesData = $surveyQuestionAnswer->getJSON();

		$tx = $this->transactionBeginner->begin();

		try {
			$knownCompanies = ArrayHelper::filter($companiesData, static fn($companyData) => ArrayHelper::keyExists($companyData, 'company_id'));

			if (ArrayHelper::notEmpty($knownCompanies)) {
				$this->createCompanySystemMessages($survey, $knownCompanies);
			}

			$this->createTask($survey, $surveyChatMemberMessage, $surveyQuestionAnswer, $companiesData);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	private function createCompanySystemMessages(Survey $survey, array $companiesData): void
	{
		$objectId = $survey->chatMember->model->object_id;

		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($companiesData as $companyData) {
				$companyId = $companyData['company_id'];
				$area      = $companyData['area'];

				$companyChatMember = $this->chatMemberRepository->getByCompanyIdOrThrow($companyId);

				$message = CompanyOnObjectChatMemberSystemMessage::create()
				                                                 ->setSurveyId($survey->id)
				                                                 ->setObjectId($objectId)
				                                                 ->setArea($area);

				if (ArrayHelper::keyExists($companyData, 'description') && !empty($companyData['description'])) {
					$message->setDescription($companyData['description']);
				}


				$this->effectSystemMessageService->createSystemMessage($companyChatMember, $survey, $message->toMessage());
			}

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
	private function createTask(Survey $survey, ChatMemberMessage $message, SurveyQuestionAnswer $surveyQuestionAnswer, array $companiesData): void
	{
		$taskMessage = $this->getTaskMessage($survey->chatMember->model, $companiesData);

		$this->effectTaskService->createTaskForMessage(
			$message,
			$survey->user,
			$surveyQuestionAnswer,
			$taskMessage
		);
	}


	private function getTaskMessage(ObjectChatMember $objectChatMember, array $companiesData): string
	{
		$companiesCount      = ArrayHelper::length($companiesData);
		$knownCompaniesCount = ArrayHelper::length(ArrayHelper::filterKeyExists($companiesData, 'company_id'));

		if ($knownCompaniesCount > 0) {
			return sprintf(self::TASK_MESSAGE_TEXT_WITH_NEW_COMPANIES, $objectChatMember->object_id, $companiesCount, $knownCompaniesCount);
		}

		return sprintf(self::TASK_MESSAGE_TEXT_WITHOUT_NEW_COMPANIES, $objectChatMember->object_id, $companiesCount);
	}
}

