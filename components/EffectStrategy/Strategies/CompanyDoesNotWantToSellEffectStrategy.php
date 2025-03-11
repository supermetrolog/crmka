<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectTaskService;
use app\helpers\ArrayHelper;
use app\helpers\StringHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use Exception;
use Throwable;

class CompanyDoesNotWantToSellEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = '%s %s - больше не продается, убрать в пассив';

	private CreateEffectTaskService $effectTaskService;

	public function __construct(CreateEffectTaskService $effectTaskService)
	{
		$this->effectTaskService = $effectTaskService;
	}

	/**
	 * @throws Exception
	 */
	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		if ($answer->surveyQuestionAnswer->hasNegativeAnswer()) {
			$chatMember = $survey->chatMember;

			return $chatMember->isObjectChatMember() && $chatMember->objectChatMember->isRentOrSale();
		}

		return false;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$saleOffers = $survey->chatMember->object->getOffers()->notDelete()->active()->saleDealType()->all();

		if (ArrayHelper::notEmpty($saleOffers)) {
			$this->effectTaskService->createTaskForMessage(
				$surveyChatMemberMessage,
				$survey->user,
				$surveyQuestionAnswer,
				$this->getTaskMessage($saleOffers)
			);
		}
	}

	/**
	 * @param \app\models\oldDb\OfferMix[] $offers
	 *
	 * @throws Exception
	 */
	public function getTaskMessage(array $offers): string
	{
		$offerIds = StringHelper::join(
			StringHelper::SPACED_COMMA,
			...ArrayHelper::map($offers, static fn($offer) => "#{$offer->visual_id}")
		);

		return sprintf(
			self::TASK_MESSAGE_TEXT,
			ArrayHelper::length($offers) > 1 ? 'Предложения' : 'Предложение',
			$offerIds
		);
	}
}