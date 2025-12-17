<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\ErrorResponse;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\AttributeRule\AttributeRuleForm;
use app\repositories\AttributeRuleRepository;
use app\resources\Attribute\AttributeRuleResource;
use app\usecases\Attribute\AttributeRuleService;
use yii\db\StaleObjectException;

class AttributeRuleController extends AppController
{
	private AttributeRuleService    $service;
	private AttributeRuleRepository $repository;

	public function __construct(
		$id,
		$module,
		AttributeRuleService $service,
		AttributeRuleRepository $repository,
		$config = [])
	{
		$this->service    = $service;
		$this->repository = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function actionView(int $id): AttributeRuleResource
	{
		$model = $this->repository->findOneOrThrow($id);

		return new AttributeRuleResource($model);
	}

	/**
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreate(): AttributeRuleResource
	{
		$form = new AttributeRuleForm();
		$form->setScenario(AttributeRuleForm::SCENARIO_CREATE);
		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new AttributeRuleResource($model);
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws ModelNotFoundException
	 */
	public function actionUpdate(int $id): AttributeRuleResource
	{
		$model = $this->repository->findOneOrThrow($id);

		$form = new AttributeRuleForm();
		$form->setScenario(AttributeRuleForm::SCENARIO_UPDATE);
		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new AttributeRuleResource($model);
	}

	/**
	 * @return ErrorResponse|SuccessResponse
	 * @throws \Throwable
	 * @throws StaleObjectException
	 */
	public function actionDelete(int $id)
	{
		try {
			$model = $this->repository->findOneOrThrow($id);

			$this->service->delete($model);

			return $this->success('Правило атрибута успешно удалено.');
		} catch (ModelNotFoundException $e) {
			return $this->error('Правило атрибута не найдено.');
		}
	}
}