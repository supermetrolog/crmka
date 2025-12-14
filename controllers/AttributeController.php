<?php

namespace app\controllers;

use app\exceptions\services\AttributeAlreadyExistsException;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\ErrorResponse;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Attribute\AttributeForm;
use app\models\forms\Attribute\AttributeOptionForm;
use app\models\search\AttributeSearch;
use app\repositories\AttributeRepository;
use app\resources\Attribute\AttributeOptionResource;
use app\resources\Attribute\AttributeResource;
use app\resources\Attribute\AttributeSearchResource;
use app\usecases\Attribute\AttributeService;
use Throwable;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;

class AttributeController extends AppController
{
	private AttributeService    $service;
	private AttributeRepository $repository;

	public function __construct(
		$id,
		$module,
		AttributeService $service,
		AttributeRepository $repository,
		$config = []
	)
	{
		$this->service    = $service;
		$this->repository = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new AttributeSearch();

		$dataProvider = $searchModel->search($this->request->get());

		return AttributeSearchResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function actionView(int $id): AttributeResource
	{
		$model = $this->repository->findOneOrThrow($id);

		return new AttributeResource($model);
	}

	/**
	 * @throws ValidateException
	 * @throws AttributeAlreadyExistsException
	 * @throws SaveModelException
	 */
	public function actionCreate(): AttributeResource
	{
		$form = new AttributeForm();
		$form->setScenario(AttributeForm::SCENARIO_CREATE);
		$form->load($this->request->post());

		$form->created_by_id = $this->user->identity->id;

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new AttributeResource($model);
	}

	/**
	 * @throws ValidateException
	 */
	public function actionCreateWithOptions(): AttributeResource
	{
		$form = new AttributeForm();

		$form->setScenario(AttributeForm::SCENARIO_CREATE);
		$form->load($this->request->post());

		$form->created_by_id = $this->user->identity->id;

		$optionDtos = [];

		foreach ($this->request->post('options', []) as $payload) {
			$optionDtos[] = $this->makeAttributeOptionForm($payload)->getDto();
		}

		$form->validateOrThrow();

		$model = $this->service->createWithOptions($form->getDto(), $optionDtos);

		return new AttributeResource($model);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionUpdate(int $id): AttributeResource
	{
		$attribute = $this->repository->findOneOrThrow($id);

		$form = new AttributeForm();
		$form->setScenario(AttributeForm::SCENARIO_UPDATE);
		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->update($attribute, $form->getDto());

		return new AttributeResource($model);
	}

	/**
	 * @return ErrorResponse|SuccessResponse
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 * @throws StaleObjectException
	 */
	public function actionDelete(int $id)
	{
		try {
			$attribute = $this->repository->findOneOrThrow($id);

			$this->service->delete($attribute);

			return $this->success('Атрибут успешно удален.');
		} catch (ModelNotFoundException $e) {
			return $this->error('Атрибут не найден.');
		}
	}

	/**
	 * @return AttributeOptionResource[]
	 * @throws ModelNotFoundException
	 */
	public function actionOptions(int $id): array
	{
		$attribute = $this->repository->findOneOrThrow($id, false);

		return AttributeOptionResource::collection($attribute->attributeOptions);
	}

	/**
	 * @throws ValidateException
	 */
	public function makeAttributeOptionForm(array $payload): AttributeOptionForm
	{
		$form = new AttributeOptionForm();

		$form->load($payload);

		$form->validateOrThrow();

		return $form;
	}
}