<?php

namespace app\controllers;

use app\exceptions\services\RequestDealAlreadyExistsException;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\mappers\Deal\DealDtoMapper;
use app\mappers\Deal\RequestDealDtoMapper;
use app\models\forms\Deal\DealForm;
use app\models\forms\Deal\RequestDealForm;
use app\repositories\DealRepository;
use app\repositories\RequestRepository;
use app\resources\Deal\DealBaseResource;
use app\resources\Deal\DealSearchResource;
use app\usecases\Deal\DealService;
use Exception;
use Throwable;
use yii\db\StaleObjectException;

class DealController extends AppController
{

	private RequestRepository    $requestRepository;
	private DealService          $dealService;
	private DealRepository       $dealRepository;
	private DealDtoMapper        $dealDtoMapper;
	private RequestDealDtoMapper $requestDealDtoMapper;

	public function __construct(
		$id,
		$module,
		RequestRepository $requestRepository,
		DealService $dealService,
		DealRepository $dealRepository,
		DealDtoMapper $dealDtoMapper,
		RequestDealDtoMapper $requestDealDtoMapper,
		array $config = []
	)
	{
		$this->requestRepository    = $requestRepository;
		$this->dealService          = $dealService;
		$this->dealRepository       = $dealRepository;
		$this->dealDtoMapper        = $dealDtoMapper;
		$this->requestDealDtoMapper = $requestDealDtoMapper;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @return DealSearchResource[]
	 */
	// TODO: DataProvider + Search
	public function actionIndex(): array
	{
		$deals = $this->dealRepository->findAll();

		return DealSearchResource::collection($deals);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function actionView(int $id): DealBaseResource
	{
		$deal = $this->dealRepository->findOneOrThrow($id);

		return new DealBaseResource($deal);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws ValidateException
	 * @throws Throwable
	 * @throws SaveModelException
	 */
	public function actionCreateForRequest(int $id)
	{
		$request = $this->requestRepository->findOneOrThrow($id);

		$form = new RequestDealForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		try {
			$deal = $this->dealService->createForRequest($request, $this->requestDealDtoMapper->fromForm($form));

			return new DealBaseResource($deal);
		} catch (RequestDealAlreadyExistsException $exception) {
			return $this->error('У запроса уже существует активная сделка');
		}
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Exception
	 */
	public function actionUpdate(int $id): DealBaseResource
	{
		$deal = $this->dealRepository->findOneOrThrow($id);

		$form = new DealForm();

		$form->setScenario(DealForm::SCENARIO_UPDATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->dealService->update($deal, $this->dealDtoMapper->fromUpdateForm($form));

		return new DealBaseResource($model);
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws ModelNotFoundException
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$deal = $this->dealRepository->findOneOrThrow($id);

		$this->dealService->delete($deal);

		return $this->success('Сделка удалена');
	}
}
