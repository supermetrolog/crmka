<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\ErrorResponse;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\AttributeOption\AttributeOptionForm;
use app\repositories\AttributeOptionRepository;
use app\resources\Attribute\AttributeOptionResource;
use app\usecases\Attribute\AttributeOptionService;
use yii\db\StaleObjectException;

class AttributeOptionController extends AppController
{
	private AttributeOptionService    $service;
	private AttributeOptionRepository $repository;

	public function __construct(
		$id,
		$module,
		AttributeOptionService $service,
		AttributeOptionRepository $repository,
		$config = [])
	{
		$this->service    = $service;
		$this->repository = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function actionView(int $id): AttributeOptionResource
	{
		$model = $this->repository->findOneOrThrow($id);

		return new AttributeOptionResource($model);
	}

	/**
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreate(): AttributeOptionResource
	{
		$form = new AttributeOptionForm();
		$form->setScenario(AttributeOptionForm::SCENARIO_CREATE);
		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new AttributeOptionResource($model);
	}

	/**
	 * @throws ValidateException
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function actionUpdate(int $id): AttributeOptionResource
	{
		$model = $this->repository->findOneOrThrow($id);

		$form = new AttributeOptionForm();
		$form->setScenario(AttributeOptionForm::SCENARIO_UPDATE);
		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new AttributeOptionResource($model);
	}

	/**
	 * @return SuccessResponse|ErrorResponse
	 * @throws ModelNotFoundException
	 * @throws \Throwable
	 * @throws StaleObjectException
	 */
	public function actionDelete(int $id)
	{
		try {
			$model = $this->repository->findOneOrThrow($id);

			$this->service->delete($model);

			return $this->success('Опция атрибута успешно удалена.');
		} catch (ModelNotFoundException $e) {
			return $this->error('Опция атрибута не найдена.');
		}
	}
}