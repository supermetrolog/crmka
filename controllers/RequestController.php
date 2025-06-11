<?php

namespace app\controllers;

use app\exceptions\ValidationErrorHttpException;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\ErrorResponse;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Request\RequestChangeConsultantForm;
use app\models\forms\Request\RequestCloneForm;
use app\models\forms\Request\RequestForm;
use app\models\forms\Request\RequestPassiveForm;
use app\models\RequestSearch;
use app\repositories\RequestRepository;
use app\resources\Company\Request\CompanyRequestFullResource;
use app\resources\Request\RequestFullResource;
use app\resources\Request\RequestSearchResource;
use app\resources\Request\RequestWithProgressResource;
use app\usecases\Request\RequestService;
use Throwable;
use yii\base\ErrorException;
use yii\base\InvalidArgumentException;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;

/**
 * RequestController implements the CRUD actions for Request model.
 */
class RequestController extends AppController
{
	private RequestRepository $requestRepository;
	private RequestService    $requestService;

	public function __construct(
		$id,
		$module,
		RequestService $requestService,
		RequestRepository $requestRepository,
		array $config = []
	)
	{
		$this->requestService    = $requestService;
		$this->requestRepository = $requestRepository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new RequestSearch();

		$dataProvider = $searchModel->search($this->request->get());

		return RequestSearchResource::fromDataProvider($dataProvider);
	}

	/**
	 * @return RequestWithProgressResource[]
	 * @throws ErrorException
	 */
	public function actionCompanyRequests($id): array
	{
		$models = $this->requestRepository->findAllByCompanyIdWithRelations((int)$id);

		return CompanyRequestFullResource::collection($models);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function actionView($id): RequestWithProgressResource
	{
		$request = $this->requestRepository->findOneOrThrowWithRelations($id);

		return new RequestWithProgressResource($request);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidationErrorHttpException
	 */
	public function actionCreate(): RequestFullResource
	{
		$form = new RequestForm();

		$form->setScenario(RequestForm::SCENARIO_CREATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$request = $this->requestService->create($form->getDto());

		return new RequestFullResource($request);
	}

	/**
	 * @throws ValidateException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function actionUpdate($id): RequestFullResource
	{
		$request = $this->requestRepository->findOneOrThrow($id);

		$form = new RequestForm();

		$form->setScenario(RequestForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$request = $this->requestService->update($request, $form->getDto());

		return new RequestFullResource($request);
	}

	/**
	 * @throws Throwable
	 * @throws ValidationErrorHttpException
	 * @throws ModelNotFoundException
	 */
	public function actionDisable($id): SuccessResponse
	{
		$request = $this->requestRepository->findOneOrThrow($id);

		$form = new RequestPassiveForm();

		$form->load($this->request->post());
		$form->validateOrThrow();

		$this->requestService->markAsPassive($request, $form->getDto());

		return $this->success('Запрос переведен в пассив');
	}

	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 */
	public function actionUndisable($id): SuccessResponse
	{
		$request = $this->requestRepository->findOneOrThrow($id);

		$this->requestService->markAsActive($request);

		return $this->success('Запрос переведен в актив');
	}

	/**
	 * @throws ForbiddenHttpException
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 * @throws ValidationErrorHttpException
	 */
	public function actionClone($id): RequestFullResource
	{
		if (!$this->user->identity->isModeratorOrHigher()) {
			throw new ForbiddenHttpException('У вас нет прав на клонирование запросов');
		}

		$request = $this->requestRepository->findOneOrThrowWithRelations($id);

		$form = new RequestCloneForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->requestService->clone($request, $form->getDto());

		return new RequestFullResource($model);
	}

	/**
	 * @return RequestWithProgressResource|ErrorResponse
	 *
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 * @throws ValidateException
	 * @throws ValidationErrorHttpException
	 */
	public function actionChangeConsultant($id)
	{
		$request = $this->requestRepository->findOneOrThrow($id);

		$form = new RequestChangeConsultantForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		try {
			$model = $this->requestService->changeConsultant($request, $form->getDto());

			return new RequestWithProgressResource($model);
		} catch (InvalidArgumentException $e) {
			return $this->error('Консультант уже назначен на этот запрос');
		}
	}
}
