<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Phone\PhoneForm;
use app\repositories\PhoneRepository;
use app\resources\Phone\PhoneResource;
use app\usecases\Phone\PhoneService;
use Throwable;
use yii\db\StaleObjectException;
use yii\web\ForbiddenHttpException;

class PhoneController extends AppController
{
	private PhoneService    $service;
	private PhoneRepository $repository;

	public function __construct(
		$id,
		$module,
		PhoneService $service,
		PhoneRepository $repository,
		array $config = []
	)
	{
		$this->service    = $service;
		$this->repository = $repository;

		parent::__construct($id, $module, $config);
	}


	/**
	 * @throws ModelNotFoundException
	 */
	public function actionView($id): PhoneResource
	{
		$phone = $this->repository->findOneOrThrow($id);

		return new PhoneResource($phone);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionUpdate($id): PhoneResource
	{
		$model = $this->repository->findOneOrThrow($id);

		$form = new PhoneForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new PhoneResource($model);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function actionDisable($id): SuccessResponse
	{
		$phone = $this->repository->findOneOrThrow($id);

		$this->service->markAsPassive($phone);

		return $this->success('Телефон переведен в пассив');
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function actionEnable($id): SuccessResponse
	{
		$phone = $this->repository->findOneOrThrow($id);

		$this->service->markAsActive($phone);

		return $this->success('Телефон отмечен как активный');
	}

	/**
	 * @throws ForbiddenHttpException
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$phone = $this->repository->findOneOrThrow($id);

		$this->service->delete($phone);

		return new SuccessResponse('Телефон успешно удален');
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function actionMarkAsMain(int $id): SuccessResponse
	{
		$phone = $this->repository->findOneOrThrow($id);

		$this->service->markAsMain($phone);

		return $this->success('Телефон отмечен как основной');
	}
}
