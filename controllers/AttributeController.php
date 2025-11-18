<?php

namespace app\controllers;

use app\exceptions\services\common\AlreadyExistsException;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\ErrorResponse;
use app\models\forms\Attribute\AttributeForm;
use app\resources\Attribute\AttributeResource;
use app\usecases\Attribute\AttributeService;
use Throwable;
use yii\db\StaleObjectException;

class AttributeController extends AppController
{
	private AttributeService $service;

	public function __construct(
		$id,
		$module,
		AttributeService $service,
		$config = []
	)
	{
		$this->service = $service;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws AlreadyExistsException
	 * @throws SaveModelException
	 */
	public function actionCreate(): AttributeResource
	{
		$form = new AttributeForm();
		$form->setScenario(AttributeForm::SCENARIO_CREATE);
		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new AttributeResource($model);
	}

	/**
	 * @return AttributeResource|ErrorResponse
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionUpdate(int $id)
	{
		$form = new AttributeForm();
		$form->setScenario(AttributeForm::SCENARIO_UPDATE);
		$form->load($this->request->post());

		$form->validateOrThrow();

		try {
			$model = $this->service->update($id, $form->getDto());
		} catch (ModelNotFoundException $e) {
			return $this->error('Атрибут не найден.');
		}

		return new AttributeResource($model);
	}

	/**
	 * @return ErrorResponse|void
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 * @throws StaleObjectException
	 */
	public function actionDelete(int $id)
	{
		try {
			$this->service->delete($id);
		} catch (ModelNotFoundException $e) {
			return $this->error('Атрибут не найден.');
		}
	}

	// TODO: attribute
}