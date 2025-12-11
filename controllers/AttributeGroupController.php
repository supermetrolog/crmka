<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\ErrorResponse;
use app\models\forms\Attribute\AttributeGroupForm;
use app\resources\Attribute\AttributeGroupResource;
use app\usecases\Attribute\AttributeGroupService;
use yii\db\StaleObjectException;

class AttributeGroupController extends AppController
{
	private AttributeGroupService $service;

	public function __construct(
		$id,
		$module,
		AttributeGroupService $service,
		$config = [])
	{
		$this->service = $service;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionCreate(): AttributeGroupResource
	{
		$form = new AttributeGroupForm();
		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new AttributeGroupResource($model);
	}

	/**
	 * @return AttributeGroupResource|ErrorResponse
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionUpdate(int $id)
	{
		$form = new AttributeGroupForm();
		$form->load($this->request->post());

		$form->validateOrThrow();

		try {
			$model = $this->service->update($id, $form->getDto());
		} catch (ModelNotFoundException $e) {
			return $this->error('Группа атрибутов не найдена.');
		}

		return new AttributeGroupResource($model);
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
			return $this->error('Группа атрибутов не найдена.');
		}
	}
}