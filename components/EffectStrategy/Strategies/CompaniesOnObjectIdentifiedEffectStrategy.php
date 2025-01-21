<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectSystemMessageService;
use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\models\ChatMemberMessage;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\repositories\ChatMemberRepository;
use app\services\ChatMemberSystemMessage\CompanyOnObjectChatMemberSystemMessage;
use Throwable;
use yii\base\Exception;

class CompaniesOnObjectIdentifiedEffectStrategy extends AbstractEffectStrategy
{
	private CreateEffectSystemMessageService $effectSystemMessageService;
	private TransactionBeginnerInterface     $transactionBeginner;
	private ChatMemberRepository             $chatMemberRepository;

	public function __construct(
		CreateEffectSystemMessageService $effectSystemMessageService,
		TransactionBeginnerInterface $transactionBeginner,
		ChatMemberRepository $chatMemberRepository
	)
	{
		$this->effectSystemMessageService = $effectSystemMessageService;
		$this->transactionBeginner        = $transactionBeginner;
		$this->chatMemberRepository       = $chatMemberRepository;
	}

	/**
	 * @throws Exception
	 */
	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		$jsonData = $answer->surveyQuestionAnswer->getJSON();

		return ArrayHelper::isArray($jsonData) && ArrayHelper::length($jsonData) > 0;
	}

	/**
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$companies = $surveyQuestionAnswer->getJSON();
		$objectId  = $survey->chatMember->model->object_id;

		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($companies as $companyData) {
				$companyId = $companyData['company_id'];
				$area      = $companyData['area'];

				$companyChatMember = $this->chatMemberRepository->getByCompanyId($companyId);

				$message = CompanyOnObjectChatMemberSystemMessage::create()
				                                                 ->setSurveyId($survey->id)
				                                                 ->setObjectId($objectId)
				                                                 ->setArea($area)
				                                                 ->toMessage();

				$this->effectSystemMessageService->createSystemMessage($companyChatMember, $survey, $message);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}

