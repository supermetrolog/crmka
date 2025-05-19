<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\TaskRelationEntity\UpdateTaskRelationEntityForm;
use app\models\TaskRelationEntity;
use app\repositories\TaskRelationEntityRepository;
use app\usecases\TaskRelationEntity\TaskRelationEntityService;
use Throwable;
use yii\db\StaleObjectException;

class TaskRelationEntityController extends AppController
{
	private TaskRelationEntityService    $service;
	private TaskRelationEntityRepository $repository;


	public function __construct(
		$id,
		$module,
		TaskRelationEntityService $service,
		TaskRelationEntityRepository $repository,
		array $config = []
	)
	{
		$this->service    = $service;
		$this->repository = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): TaskRelationEntity
	{
		$entity = $this->repository->findOneOrThrow($id);

		$form = new UpdateTaskRelationEntityForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->update($entity, $form->getDto());

		return new TaskRelationEntity($model);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$entity = $this->repository->findOneOrThrow($id);

		$this->service->delete($entity, $this->user->identity);

		return new SuccessResponse('Связь удалена');
	}
}
