<?php

namespace app\components\EffectStrategy\Strategies;

use app\builders\Task\TaskBuilderFactory;
use app\components\EffectStrategy\AbstractEffectStrategy;
use app\dto\Task\LinkTaskRelationEntityDto;
use app\helpers\ArrayHelper;
use app\helpers\TypeConverterHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Block;
use app\models\ChatMemberMessage;
use app\models\Objects;
use app\models\QuestionAnswer;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\models\Task;
use app\models\TaskRelationEntity;
use app\usecases\ChatMember\ChatMemberService;
use app\usecases\Task\CreateTaskService;
use Throwable;
use yii\base\ErrorException;
use yii\base\Exception;

class CompanyOffersChangesEffectStrategy extends AbstractEffectStrategy
{
	private const NEW_OFFERS_TASK_TITLE   = 'Комп. "%s", %d новых предложений на объекте #%d';
	private const NEW_OFFERS_TASK_MESSAGE = 'Подробности в прикрепленном опросе';

	private const DISABLE_CURRENT_OFFERS_TASK_TITLE = 'Отправить в пассив предложения по объекту #%d (комп. "%d")';
	private const CURRENT_OFFERS_TASK_MESSAGE       = 'Подробности в прикрепленном опросе';

	private const OFFER_MUST_BE_EDITED_TITLE_TASK   = 'Комп. "%s", объект #%d. Редактировать предложение #%d';
	private const OFFER_MUST_BE_DISABLED_TITLE_TASK = 'Комп. "%s", объект #%d. Архивировать предложение #%d';
	private const OBJECT_SOLD_TITLE_TASK            = 'Комп. "%s", объект #%d продан, отвязать от компании';
	private const OBJECT_DESTROYED_TITLE_TASK       = 'Комп. "%s", объект #%d снесен, убрать из компании';

	private const ANSWER_OBJECT_WITHOUT_CHANGES         = 1;
	private const ANSWER_OBJECT_OFFERS_MUST_BE_DISABLED = 2;
	private const ANSWER_OBJECT_SKIPPED                 = 3;
	private const ANSWER_OBJECT_COMPLETED               = 4;
	private const ANSWER_OBJECT_SOLD                    = 5;
	private const ANSWER_OBJECT_DESTROYED               = 6;

	private const ANSWER_OFFER_WITHOUT_CHANGES  = 1;
	private const ANSWER_OFFER_MUST_BE_DISABLED = 2;
	private const ANSWER_OFFER_MUST_BE_EDITED   = 3;

	private TaskBuilderFactory           $taskBuilderFactory;
	private TransactionBeginnerInterface $transactionBeginner;
	private ChatMemberService            $chatMemberService;
	private CreateTaskService            $createTaskService;

	public function __construct(
		TaskBuilderFactory $taskBuilderFactory,
		TransactionBeginnerInterface $transactionBeginner,
		ChatMemberService $chatMemberService,
		CreateTaskService $createTaskService
	)
	{
		$this->taskBuilderFactory  = $taskBuilderFactory;
		$this->transactionBeginner = $transactionBeginner;
		$this->chatMemberService   = $chatMemberService;
		$this->createTaskService   = $createTaskService;
	}

	/**
	 * @throws Exception
	 * @throws \Exception
	 */
	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		if ($answer->surveyQuestionAnswer->hasAnswer()) {
			$jsonData = $answer->surveyQuestionAnswer->getJSON();

			return ArrayHelper::isArray($jsonData) || ArrayHelper::notEmpty($jsonData);
		}

		return false;
	}

	/**
	 * @throws Exception
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$jsonData = $surveyQuestionAnswer->getJSON();

		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($jsonData as $key => $payload) {
				/** @var Objects $object */
				$object = Objects::find()->byId(TypeConverterHelper::toInt($key))->oneOrThrow();

				$this->handleObject($object, $payload, $survey);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws ErrorException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 */
	private function handleObject(Objects $object, array $payload, Survey $survey): void
	{
		$answer = ArrayHelper::getValue($payload, 'answer');

		if (is_null($answer)) {
			if ($survey->isCompleted()) {
				$this->markObjectAsCalled($object, $survey);
			}

			return;
		}

		$answer = TypeConverterHelper::toInt($answer);

		if ($answer === self::ANSWER_OBJECT_SKIPPED) {
			return;
		}

		$tx = $this->transactionBeginner->begin();

		try {
			$this->markObjectAsCalled($object, $survey);

			switch ($answer) {
				case self::ANSWER_OBJECT_OFFERS_MUST_BE_DISABLED:
				{
					$this->createDisableObjectOffersTask($object, $survey);
					break;
				}
				case self::ANSWER_OBJECT_COMPLETED:
				{
					$this->handleObjectOffers($object, $payload, $survey);
					break;
				}
				case self::ANSWER_OBJECT_SOLD:
				{
					$this->markObjectAsSold($object, $survey);
					break;
				}
				case self::ANSWER_OBJECT_DESTROYED:
				{
					$this->markObjectAsDestroyed($object, $survey);
					break;
				}
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws ErrorException
	 * @throws \Exception
	 * @throws Throwable
	 */
	private function handleObjectOffers(Objects $object, array $payload, Survey $survey): void
	{
		$currentOffers = ArrayHelper::getValue($payload, 'current', []);
		$createdOffers = ArrayHelper::getValue($payload, 'created', []);

		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($currentOffers as $offerPayload) {
				$this->handleObjectOffer($object, $offerPayload, $survey);
			}

			if (ArrayHelper::notEmpty($createdOffers)) {
				$this->createCompanyOffersTask($object, $createdOffers, $survey);
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
	private function markObjectAsCalled(Objects $object, Survey $survey): void
	{
		$call = $survey->mainCall;

		if (is_null($call)) {
			return;
		}

		$chatMembers = $object->chatMembers;

		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($chatMembers as $chatMember) {
				$this->chatMemberService->linkCall($chatMember, $call);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws ErrorException
	 */
	private function findObjectOfferById(int $id): ?Block
	{
		return Block::find()->andWhere([Block::field('id') => $id])->one();
	}

	/**
	 * @throws \Exception
	 * @throws Throwable
	 */
	private function handleObjectOffer(Objects $object, array $payload, Survey $survey): void
	{
		$answer = ArrayHelper::getValue($payload, 'answer');

		if (is_null($answer)) {
			return;
		}

		$answer      = TypeConverterHelper::toInt($answer);
		$description = ArrayHelper::getValue($payload, 'description');

		$offerId = TypeConverterHelper::toInt(ArrayHelper::getValue($payload, 'offer_id'));

		$block = $this->findObjectOfferById($offerId);

		switch ($answer) {
			case self::ANSWER_OFFER_MUST_BE_DISABLED:
			{
				$title = sprintf(self::OFFER_MUST_BE_DISABLED_TITLE_TASK,
					$survey->chatMember->company->getShortName(), $object->id, $offerId
				);

				$this->createHandlingOfferTask($object, $survey, $title, $description, $block);

				break;
			}
			case self::ANSWER_OFFER_MUST_BE_EDITED:
			{
				$title = sprintf(self::OFFER_MUST_BE_EDITED_TITLE_TASK,
					$survey->chatMember->company->getShortName(), $object->id, $offerId
				);

				$this->createHandlingOfferTask($object, $survey, $title, $description, $block);

				break;
			}
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 */
	private function createHandlingOfferTask(Objects $object, Survey $survey, string $title, ?string $message = null, ?Block $block = null): void
	{
		$dto = $this->taskBuilderFactory->createEffectBuilder()
		                                ->setType(Task::TYPE_OBJECT_HANDLING)
		                                ->setCreatedBy($survey->user)
		                                ->setTitle($title)
		                                ->setMessage($message)
		                                ->build();

		$relationsDtos = [
			$this->makeTaskRelationEntityDto($survey->chatMember->company),
			$this->makeTaskRelationEntityDto($survey),
			$this->makeTaskRelationEntityDto($object)
		];

		if ($block) {
			$relationsDtos[] = $this->makeTaskRelationEntityDto($block);
		}

		$this->createTaskService->create($dto, [], $relationsDtos);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 */
	private function createDisableObjectOffersTask(Objects $object, Survey $survey): void
	{
		$title = sprintf(self::DISABLE_CURRENT_OFFERS_TASK_TITLE, $object->id, $survey->chatMember->company->getShortName());

		$this->createHandlingOfferTask($object, $survey, $title, self::CURRENT_OFFERS_TASK_MESSAGE);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 */
	private function createCompanyOffersTask(Objects $object, array $offersPayload, Survey $survey): void
	{
		$title = sprintf(self::NEW_OFFERS_TASK_TITLE, $survey->chatMember->company->getShortName(), ArrayHelper::length($offersPayload), $object->id);

		$dto = $this->taskBuilderFactory->createEffectBuilder()
		                                ->setType(Task::TYPE_OBJECT_HANDLING)
		                                ->setCreatedBy($survey->user)
		                                ->setTitle($title)
		                                ->setMessage(self::NEW_OFFERS_TASK_MESSAGE)
		                                ->build();

		$relationsDtos = [
			$this->makeTaskRelationEntityDto($survey->chatMember->company),
			$this->makeTaskRelationEntityDto($survey),
			$this->makeTaskRelationEntityDto($object)
		];

		$this->createTaskService->create($dto, [], $relationsDtos);
	}

	private function makeTaskRelationEntityDto($entity): LinkTaskRelationEntityDto
	{
		return new LinkTaskRelationEntityDto([
			'entityId'     => $entity->id,
			'entityType'   => $entity::getMorphClass(),
			'relationType' => TaskRelationEntity::RELATION_TYPE_SYSTEM
		]);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function markObjectAsSold(Objects $object, Survey $survey): void
	{
		$title = sprintf(self::OBJECT_SOLD_TITLE_TASK, $object->id, $survey->chatMember->company->getShortName());

		$this->createHandlingOfferTask($object, $survey, $title);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function markObjectAsDestroyed(Objects $object, Survey $survey): void
	{
		$title = sprintf(self::OBJECT_DESTROYED_TITLE_TASK, $object->id, $survey->chatMember->company->getShortName());

		$this->createHandlingOfferTask($object, $survey, $title);
	}
}