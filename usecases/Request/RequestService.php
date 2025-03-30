<?php

declare(strict_types=1);

namespace app\usecases\Request;

use app\components\EventManager;
use app\dto\Request\CloneRequestDto;
use app\dto\Request\CreateRequestDto;
use app\dto\Request\PassiveRequestDto;
use app\dto\Request\RequestRelationsDto;
use app\dto\Request\UpdateRequestDto;
use app\dto\Timeline\CreateTimelineDto;
use app\events\NotificationEvent;
use app\events\Request\CreateRequestEvent;
use app\events\Request\RequestActivatedEvent;
use app\events\Request\RequestDeactivatedEvent;
use app\exceptions\ValidationErrorHttpException;
use app\helpers\ArrayHelper;
use app\helpers\DateTimeHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\AR\AR;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Notification;
use app\models\Request;
use app\models\Timeline;
use app\usecases\Request\Relations\RequestDirectionRelationService;
use app\usecases\Request\Relations\RequestDistrictRelationService;
use app\usecases\Request\Relations\RequestGateTypeRelationService;
use app\usecases\Request\Relations\RequestObjectClassRelationService;
use app\usecases\Request\Relations\RequestObjectTypeGeneralRelationService;
use app\usecases\Request\Relations\RequestObjectTypeRelationService;
use app\usecases\Request\Relations\RequestRegionRelationService;
use app\usecases\Timeline\TimelineService;
use Throwable;
use Yii;
use yii\base\ErrorException;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;

class RequestService
{
	private TransactionBeginnerInterface            $transactionBeginner;
	private EventManager                            $eventManager;
	private TimelineService                         $timelineService;
	private RequestDirectionRelationService         $directionRelationService;
	private RequestDistrictRelationService          $districtRelationService;
	private RequestGateTypeRelationService          $gateTypeRelationService;
	private RequestObjectTypeRelationService        $objectTypeRelationService;
	private RequestObjectTypeGeneralRelationService $objectTypeGeneralRelationService;
	private RequestObjectClassRelationService       $objectClassRelationService;
	private RequestRegionRelationService            $regionRelationService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		EventManager $eventManager,
		TimelineService $timelineService,
		RequestDirectionRelationService $directionRelationService,
		RequestDistrictRelationService $districtRelationService,
		RequestGateTypeRelationService $gateTypeRelationService,
		RequestObjectTypeRelationService $objectTypeRelationService,
		RequestObjectTypeGeneralRelationService $objectTypeGeneralRelationService,
		RequestObjectClassRelationService $objectClassRelationService,
		RequestRegionRelationService $regionRelationService
	)
	{
		$this->transactionBeginner              = $transactionBeginner;
		$this->eventManager                     = $eventManager;
		$this->timelineService                  = $timelineService;
		$this->directionRelationService         = $directionRelationService;
		$this->districtRelationService          = $districtRelationService;
		$this->gateTypeRelationService          = $gateTypeRelationService;
		$this->objectTypeRelationService        = $objectTypeRelationService;
		$this->objectTypeGeneralRelationService = $objectTypeGeneralRelationService;
		$this->objectClassRelationService       = $objectClassRelationService;
		$this->regionRelationService            = $regionRelationService;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidationErrorHttpException
	 */
	public function create(CreateRequestDto $dto): Request
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$request = new Request([
				'status'                        => Request::STATUS_ACTIVE,
				'company_id'                    => $dto->company_id,
				'name'                          => $dto->name,
				'description'                   => $dto->description,
				'contact_id'                    => $dto->contact_id,
				'consultant_id'                 => $dto->consultant_id,
				'dealType'                      => $dto->dealType,
				'minArea'                       => $dto->minArea,
				'maxArea'                       => $dto->maxArea,
				'minCeilingHeight'              => $dto->minCeilingHeight,
				'maxCeilingHeight'              => $dto->maxCeilingHeight,
				'distanceFromMKAD'              => $dto->distanceFromMKAD,
				'distanceFromMKADnotApplicable' => $dto->distanceFromMKADnotApplicable,
				'firstFloorOnly'                => $dto->firstFloorOnly,
				'expressRequest'                => $dto->expressRequest,
				'heated'                        => $dto->heated,
				'water'                         => $dto->water,
				'sewerage'                      => $dto->sewerage,
				'gaz'                           => $dto->gaz,
				'steam'                         => $dto->steam,
				'shelving'                      => $dto->shelving,
				'haveCranes'                    => $dto->haveCranes,
				'pricePerFloor'                 => $dto->pricePerFloor,
				'antiDustOnly'                  => $dto->antiDustOnly,
				'trainLine'                     => $dto->trainLine,
				'trainLineLength'               => $dto->trainLineLength,
				'electricity'                   => $dto->electricity,
				'unknownMovingDate'             => $dto->unknownMovingDate,
				'outside_mkad'                  => $dto->outside_mkad,
				'region_neardy'                 => $dto->region_neardy,
				'movingDate'                    => DateTimeHelper::tryFormat($dto->movingDate),
			]);

			$request->saveOrThrow();

			$this->createRelations($request, new RequestRelationsDto([
				'direction_ids'           => $dto->direction_ids,
				'district_ids'            => $dto->district_ids,
				'gate_types'              => $dto->gate_types,
				'object_classes'          => $dto->object_classes,
				'object_type_ids'         => $dto->object_type_ids,
				'object_type_general_ids' => $dto->object_type_general_ids,
				'region_ids'              => $dto->region_ids
			]));

			$this->createMainTimeline($request);

			$this->eventManager->trigger(new CreateRequestEvent($request));

			// TODO: Вынести в Listener + render service

			$request->trigger(Request::REQUEST_CREATED_EVENT, new NotificationEvent([
				'consultant_id' => $request->consultant_id,
				'type'          => Notification::TYPE_REQUEST_INFO,
				'title'         => 'запрос',
				'body'          => Yii::$app->controller->renderFile('@app/views/notifications_template/assigned_request.php', ['model' => $request])
			]));

			$tx->commit();

			return $request;
		} catch (Throwable $e) {
			$tx->rollback();
			throw $e;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function createMainTimeline(Request $request): Timeline
	{
		return $this->timelineService->create(
			new CreateTimelineDto([
				'request_id'    => $request->id,
				'consultant_id' => $request->consultant_id
			])
		);
	}

	/**
	 * Create new timeline for new request consultant or set as active if exists passive timeline
	 *
	 * @throws ErrorException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function actualizeMainTimeline(Request $request): void
	{
		$secondaryTimelines = $request->getActiveTimelines()
		                              ->andWhere(['!=', Timeline::field('consultant_id'), $request->consultant_id])
		                              ->all();

		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($secondaryTimelines as $timeline) {
				$this->timelineService->markAsPassive($timeline);
			}

			$mainTimeline = $request->mainTimeline;

			if ($mainTimeline && $mainTimeline->isPassive()) {
				$this->timelineService->markAsActive($mainTimeline);
			} else {
				$this->createMainTimeline($request);
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
	public function update(Request $request, UpdateRequestDto $dto): Request
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$oldConsultantId = $request->consultant_id;

			$request->load([
				'status'                        => $dto->status,
				'name'                          => $dto->name,
				'description'                   => $dto->description,
				'contact_id'                    => $dto->contact_id,
				'consultant_id'                 => $dto->consultant_id,
				'dealType'                      => $dto->dealType,
				'minArea'                       => $dto->minArea,
				'maxArea'                       => $dto->maxArea,
				'minCeilingHeight'              => $dto->minCeilingHeight,
				'maxCeilingHeight'              => $dto->maxCeilingHeight,
				'distanceFromMKAD'              => $dto->distanceFromMKAD,
				'distanceFromMKADnotApplicable' => $dto->distanceFromMKADnotApplicable,
				'firstFloorOnly'                => $dto->firstFloorOnly,
				'expressRequest'                => $dto->expressRequest,
				'heated'                        => $dto->heated,
				'water'                         => $dto->water,
				'sewerage'                      => $dto->sewerage,
				'gaz'                           => $dto->gaz,
				'steam'                         => $dto->steam,
				'shelving'                      => $dto->shelving,
				'haveCranes'                    => $dto->haveCranes,
				'pricePerFloor'                 => $dto->pricePerFloor,
				'antiDustOnly'                  => $dto->antiDustOnly,
				'trainLine'                     => $dto->trainLine,
				'trainLineLength'               => $dto->trainLineLength,
				'electricity'                   => $dto->electricity,
				'unknownMovingDate'             => $dto->unknownMovingDate,
				'outside_mkad'                  => $dto->outside_mkad,
				'region_neardy'                 => $dto->region_neardy,
				'movingDate'                    => DateTimeHelper::tryFormat($dto->movingDate),
				'passive_why'                   => $dto->passive_why,
				'passive_why_comment'           => $dto->passive_why_comment
			]);

			$request->saveOrThrow();

			$this->updateRelations($request, new RequestRelationsDto([
				'direction_ids'           => $dto->direction_ids,
				'district_ids'            => $dto->district_ids,
				'gate_types'              => $dto->gate_types,
				'object_classes'          => $dto->object_classes,
				'object_type_ids'         => $dto->object_type_ids,
				'object_type_general_ids' => $dto->object_type_general_ids,
				'region_ids'              => $dto->region_ids
			]));

			if ($oldConsultantId !== $dto->consultant_id) {
				$this->actualizeMainTimeline($request);

				// TODO: Вынести в Listener + render service

				$request->trigger(Request::REQUEST_CREATED_EVENT, new NotificationEvent([
					'consultant_id' => $request->consultant_id,
					'type'          => Notification::TYPE_REQUEST_INFO,
					'title'         => 'запрос',
					'body'          => Yii::$app->controller->renderFile('@app/views/notifications_template/assigned_request.php', ['model' => $request])
				]));
				$request->trigger(Request::REQUEST_CREATED_EVENT, new NotificationEvent([
					'consultant_id' => $oldConsultantId,
					'type'          => Notification::TYPE_REQUEST_INFO,
					'title'         => 'запрос',
					'body'          => Yii::$app->controller->renderFile('@app/views/notifications_template/unAssigned_request.php', ['model' => $request])
				]));
			}

			$tx->commit();

			return $request;
		} catch (Throwable $e) {
			$tx->rollback();
			throw $e;
		}
	}

	/**
	 * @throws SaveModelException
	 */
	public function changeStatus(Request $request, int $status): Request
	{
		$request->status = $status;
		$request->saveOrThrow();

		return $request;
	}

	/**
	 * @throws SaveModelException|Throwable
	 */
	public function markAsActive(Request $request): void
	{
		if (!$request->isActive()) {
			throw new InvalidArgumentException('Request is already active');
		}

		$tx = $this->transactionBeginner->begin();

		try {
			$request->status              = Request::STATUS_ACTIVE;
			$request->passive_why         = null;
			$request->passive_why_comment = null;

			$request->saveOrThrow();

			$this->eventManager->trigger(new RequestActivatedEvent($request));

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
	public function markAsPassive(Request $request, PassiveRequestDto $dto): void
	{
		if ($request->isPassive()) {
			throw new InvalidArgumentException('Request is already passive');
		}

		$tx = $this->transactionBeginner->begin();

		try {
			$request->status              = Request::STATUS_PASSIVE;
			$request->passive_why         = $dto->passive_why;
			$request->passive_why_comment = $dto->passive_why_comment;

			$request->saveOrThrow();

			$this->eventManager->trigger(new RequestDeactivatedEvent($request));

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 */
	public function markAsCompleted(Request $request): void
	{
		if ($request->isCompleted()) {
			throw new InvalidArgumentException('Request is already completed');
		}

		if ($request->isPassive()) {
			$request->passive_why         = null;
			$request->passive_why_comment = null;
		}

		$request->status = Request::STATUS_DONE;
		$request->saveOrThrow();
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function createRelations(Request $request, RequestRelationsDto $dto): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->directionRelationService->createRelations($request, $dto->direction_ids);
			$this->districtRelationService->createRelations($request, $dto->district_ids);
			$this->gateTypeRelationService->createRelations($request, $dto->gate_types);
			$this->objectClassRelationService->createRelations($request, $dto->object_classes);
			$this->objectTypeRelationService->createRelations($request, $dto->object_type_ids);
			$this->objectTypeGeneralRelationService->createRelations($request, $dto->object_type_general_ids);
			$this->regionRelationService->createRelations($request, $dto->region_ids);

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
	private function updateRelations(Request $request, RequestRelationsDto $dto): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->directionRelationService->updateRelations($request, $dto->direction_ids);
			$this->districtRelationService->updateRelations($request, $dto->district_ids);
			$this->gateTypeRelationService->updateRelations($request, $dto->gate_types);
			$this->objectClassRelationService->updateRelations($request, $dto->object_classes);
			$this->objectTypeRelationService->updateRelations($request, $dto->object_type_ids);
			$this->objectTypeGeneralRelationService->updateRelations($request, $dto->object_type_general_ids);
			$this->regionRelationService->updateRelations($request, $dto->region_ids);

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	public function updateRelatedTimestamp(Request $request): void
	{
		$request->updateAttributes(['related_updated_at' => DateTimeHelper::nowf()]);
	}

	/**
	 * @param array<AR|ActiveRecord> $relations
	 *
	 * @return array<string|int>
	 */
	private function mapRelations(array $relations, string $attribute): array
	{
		return ArrayHelper::map($relations, static fn($el) => $el->$attribute);
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidationErrorHttpException
	 * @throws Throwable
	 */
	public function clone(Request $request, CloneRequestDto $dto): Request
	{
		return $this->create(new CreateRequestDto([
			'consultant_id' => $dto->consultant->id,

			'company_id'                    => $request->company_id,
			'name'                          => $request->name,
			'description'                   => $request->description,
			'contact_id'                    => $request->contact_id,
			'dealType'                      => $request->dealType,
			'minArea'                       => $request->minArea,
			'maxArea'                       => $request->maxArea,
			'minCeilingHeight'              => $request->minCeilingHeight,
			'maxCeilingHeight'              => $request->maxCeilingHeight,
			'distanceFromMKAD'              => $request->distanceFromMKAD,
			'distanceFromMKADnotApplicable' => $request->distanceFromMKADnotApplicable,
			'firstFloorOnly'                => $request->firstFloorOnly,
			'expressRequest'                => $request->expressRequest,
			'heated'                        => $request->heated,
			'water'                         => $request->water,
			'sewerage'                      => $request->sewerage,
			'gaz'                           => $request->gaz,
			'steam'                         => $request->steam,
			'shelving'                      => $request->shelving,
			'haveCranes'                    => $request->haveCranes,
			'pricePerFloor'                 => $request->pricePerFloor,
			'antiDustOnly'                  => $request->antiDustOnly,
			'trainLine'                     => $request->trainLine,
			'trainLineLength'               => $request->trainLineLength,
			'electricity'                   => $request->electricity,
			'unknownMovingDate'             => $request->unknownMovingDate,
			'outside_mkad'                  => $request->outside_mkad,
			'region_neardy'                 => $request->region_neardy,
			'movingDate'                    => DateTimeHelper::tryFormat($request->movingDate),

			'direction_ids'           => $this->mapRelations($request->directions, 'direction'),
			'district_ids'            => $this->mapRelations($request->districts, 'district'),
			'gate_types'              => $this->mapRelations($request->gateTypes, 'gate_type'),
			'object_classes'          => $this->mapRelations($request->objectClasses, 'object_class'),
			'object_type_ids'         => $this->mapRelations($request->objectTypes, 'object_type'),
			'object_type_general_ids' => $this->mapRelations($request->objectTypesGeneral, 'type'),
			'region_ids'              => $this->mapRelations($request->regions, 'region'),
		]));
	}
}