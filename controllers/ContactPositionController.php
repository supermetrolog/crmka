<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Contact\ContactPositionForm;
use app\repositories\ContactPositionRepository;
use app\resources\Contact\Position\ContactPositionResource;
use app\usecases\ContactPosition\ContactPositionService;
use Throwable;
use yii\db\StaleObjectException;

class ContactPositionController extends AppController
{
	private ContactPositionRepository $repository;
	private ContactPositionService    $service;

	public function __construct(
		$id,
		$module,
		ContactPositionRepository $repository,
		ContactPositionService $service,
		array $config = []
	)
	{
		$this->repository = $repository;
		$this->service    = $service;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @return ContactPositionResource[]
	 */
	public function actionIndex(): array
	{
		return ContactPositionResource::collection($this->repository->findAll());
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionCreate(): ContactPositionResource
	{
		$form = new ContactPositionForm();

		$form->setScenario(ContactPositionForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->created_by_id = $this->user->id;

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new ContactPositionResource($model);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate($id): ContactPositionResource
	{
		$contactPosition = $this->repository->findOneOrThrow($id);

		$form = new ContactPositionForm();

		$form->setScenario(ContactPositionForm::SCENARIO_UPDATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->update($contactPosition, $form->getDto());

		return new ContactPositionResource($model);
	}


	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 */
	public function actionDelete($id): SuccessResponse
	{
		$this->service->delete($this->repository->findOneOrThrow($id));

		return $this->success('Контакт успешно удален');
	}
}
