<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\ErrorResponse;
use app\models\forms\Attribute\AttributeValueForm;
use app\resources\Attribute\AttributeValueResource;
use app\usecases\Attribute\AttributeValueService;
use yii\db\StaleObjectException;

class AttributeValueController extends AppController
{
	private AttributeValueService $service;

	public function __construct(
		$id,
		$module,
		AttributeValueService $service,
		$config = [])
	{
		$this->service = $service;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreate(): AttributeValueResource
	{
		$form = new AttributeValueForm();
		$form->setScenario(AttributeValueForm::SCENARIO_CREATE);
		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new AttributeValueResource($model);
	}

	/**
	 * @return AttributeValueResource|ErrorResponse
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id)
	{
		$form = new AttributeValueForm();
		$form->setScenario(AttributeValueForm::SCENARIO_UPDATE);
		$form->load($this->request->post());

		$form->validateOrThrow();

		try {
			$model = $this->service->update($id, $form->getDto());
		} catch (ModelNotFoundException $e) {
			return $this->error('Значение атрибута не найдено.');
		}

		return new AttributeValueResource($model);
	}

	/**
	 * @return ErrorResponse|void
	 * @throws StaleObjectException
	 * @throws \Throwable
	 */
	public function actionDelete(int $id)
	{
		try {
			$this->service->delete($id);
		} catch (ModelNotFoundException $e) {
			return $this->error('Значение атрибута не найдено.');
		}
	}
}