<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\components\EffectStrategy\Service\CreateEffectTaskService;
use app\helpers\ArrayHelper;
use app\helpers\StringHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\ObjectChatMember;
use app\models\oldDb\OfferMix;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use Throwable;
use yii\base\Exception;

class ObjectHasFreeAreaEffectStrategy extends AbstractEffectStrategy
{
	private const TASK_MESSAGE_TEXT = 'Аренда на объекте #%s не актуальна, убрать в пассив предложения: %s.';

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
		$object = $survey->chatMember->model->object;

		$rentOffers = $object->getOffers()->rentDealType()->notDelete()->active()->all();

		if (ArrayHelper::notEmpty($rentOffers)) {
			$this->createTask($survey, $surveyQuestionAnswer, $surveyChatMemberMessage, $rentOffers);
		}
	}

	/**
	 * @param OfferMix[] $offers
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function createTask(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage, array $offers): void
	{
		$chatMemberModel = $survey->chatMember->model;

		$taskMessage = $this->getTaskMessage($chatMemberModel, $offers);

		$this->effectTaskService->createTaskForMessage(
			$surveyChatMemberMessage,
			$survey->user,
			$surveyQuestionAnswer,
			$taskMessage
		);
	}

	/**
	 * @param OfferMix[] $offers
	 */
	private function getTaskMessage(ObjectChatMember $objectChatMember, array $offers): string
	{
		$offersIdsText = StringHelper::join(StringHelper::SPACED_COMMA, ...ArrayHelper::map($offers, static fn($offer) => "#$offer->visual_id"));

		return sprintf(self::TASK_MESSAGE_TEXT, $objectChatMember->object_id, $offersIdsText);
	}
}