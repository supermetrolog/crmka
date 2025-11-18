<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\models\forms\AttributeOption\AttributeOptionForm;
use app\resources\AttributeOption\AttributeOptionResource;
use app\usecases\AttributeOption\AttributeOptionService;
use yii\db\StaleObjectException;

class AttributeOptionController extends AppController
{
	private AttributeOptionService $service;

	public function __construct(
		$id,
		$module,
		AttributeOptionService $service,
		$config = [])
	{
		$this->service = $service;

		parent::__construct($id, $module, $config);
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
		$form = new AttributeOptionForm();
		$form->setScenario(AttributeOptionForm::SCENARIO_UPDATE);
		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->update($id, $form->getDto());

		return new AttributeOptionResource($model);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws \Throwable
	 * @throws StaleObjectException
	 */
	public function delete(int $id)
	{
		$this->service->delete($id);
	}

	// TODO: attribute
}