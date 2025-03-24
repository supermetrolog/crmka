<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Contact\ContactCommentForm;
use app\repositories\ContactCommentRepository;
use app\resources\Contact\Comment\ContactCommentResource;
use app\usecases\Contact\ContactCommentService;
use Throwable;
use yii\db\StaleObjectException;
use yii\web\ForbiddenHttpException;

class ContactCommentController extends AppController
{
	private ContactCommentService    $service;
	private ContactCommentRepository $repository;


	public function __construct(
		$id,
		$module,
		ContactCommentService $service,
		ContactCommentRepository $repository,
		array $config = []
	)
	{
		$this->service    = $service;
		$this->repository = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ForbiddenHttpException
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): ContactCommentResource
	{
		$model = $this->repository->findModelByIdOrThrow($id);

		$identity = $this->user->identity;

		if ($model->author_id !== $identity->id && !$identity->isAdministrator()) {
			throw new ForbiddenHttpException('У вас нет прав на редактирование этого комментария.');
		}

		$form = new ContactCommentForm();

		$form->author_id = $identity->id;

		$form->setScenario(ContactCommentForm::SCENARIO_UPDATE);
		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new ContactCommentResource($model);
	}

	/**
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 * @throws StaleObjectException
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$model = $this->repository->findModelByIdOrThrow($id);

		$identity = $this->user->identity;

		if ($model->author_id !== $identity->id && !$identity->isModeratorOrHigher()) {
			throw new ForbiddenHttpException('У вас нет прав на удаление этого комментария.');
		}

		$this->service->delete($model);

		return $this->success('Комментарий успешно удален');
	}
}
