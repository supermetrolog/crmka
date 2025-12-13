<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\ErrorResponse;
use app\models\forms\Attribute\AttributeRuleForm;
use app\resources\Attribute\AttributeRuleResource;
use app\usecases\Attribute\AttributeRuleService;
use yii\db\StaleObjectException;

class AttributeRuleController extends AppController
{
	private AttributeRuleService $service;

	public function __construct(
		$id,
		$module,
		AttributeRuleService $service,
		$config = [])
	{
		$this->service = $service;

		parent::__construct($id, $module, $config);
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
	 * @return AttributeRuleResource|ErrorResponse
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id)
	{
		$form = new AttributeRuleForm();
		$form->setScenario(AttributeRuleForm::SCENARIO_UPDATE);
		$form->load($this->request->post());

		$form->validateOrThrow();

		try {
			$model = $this->service->update($id, $form->getDto());
		} catch (ModelNotFoundException $e) {
			return $this->error('Правило атрибута не найдено.');
		}

		return new AttributeRuleResource($model);
	}

	/**
	 * @return ErrorResponse|void
	 * @throws \Throwable
	 * @throws StaleObjectException
	 */
	public function actionDelete(int $id)
	{
		try {
			$this->service->delete($id);
		} catch (ModelNotFoundException $e) {
			return $this->error('Правило атрибута не найдено.');
		}
	}
}