<?php

namespace app\components\EffectStrategy;

use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMember;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\repositories\ChatMemberRepository;
use app\services\ChatMemberSystemMessage\CompanyOnObjectChatMemberSystemMessage;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use yii\base\Exception;

class CompaniesOnObjectIdentifiedEffectStrategy implements EffectStrategyInterface
{
	private ChatMemberMessageService     $chatMemberMessageService;
	private TransactionBeginnerInterface $transactionBeginner;
	private ChatMemberRepository         $chatMemberRepository;

	public function __construct(
		ChatMemberMessageService $chatMemberMessageService,
		TransactionBeginnerInterface $transactionBeginner,
		ChatMemberRepository $chatMemberRepository
	)
	{
		$this->chatMemberMessageService = $chatMemberMessageService;
		$this->transactionBeginner      = $transactionBeginner;
		$this->chatMemberRepository     = $chatMemberRepository;
	}

	/**
	 * @throws Exception
	 * @throws Throwable
	 */
	public function handle(Survey $survey, QuestionAnswer $answer): void
	{
		$surveyQuestionAnswer  = $answer->surveyQuestionAnswer;
		$jsonData              = $surveyQuestionAnswer->getJSON();
		$effectShouldBeProcess = ArrayHelper::isArray($jsonData) && ArrayHelper::length($jsonData) > 0;

		if ($effectShouldBeProcess) {
			$this->process($survey, $jsonData);
		}
	}

	/**
	 * @throws Throwable
	 */
	private function process(Survey $survey, array $companiesData): void
	{
		$objectId = $survey->chatMember->model->object_id;

		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($companiesData as $companyData) {
				$companyId = $companyData['company_id'];
				$area      = $companyData['area'];

				$companyChatMember = $this->chatMemberRepository->getByCompanyId($companyId);

				$message = CompanyOnObjectChatMemberSystemMessage::create()
				                                                 ->setSurveyId($survey->id)
				                                                 ->setObjectId($objectId)
				                                                 ->setArea($area)
				                                                 ->toMessage();

				$this->sendSystemMessage($companyChatMember, $message, $survey);
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
	private function sendSystemMessage(ChatMember $chatMember, string $message, Survey $survey): void
	{
		$dto = new CreateChatMemberSystemMessageDto([
			'message'   => $message,
			'to'        => $chatMember,
			'surveyIds' => [$survey->id]
		]);

		$this->chatMemberMessageService->createSystemMessage($dto);
	}
}

