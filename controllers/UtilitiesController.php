<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Utilities\UtilitiesFixPurposesForm;
use app\models\forms\Utilities\UtilitiesTransferCompaniesToConsultantForm;
use app\repositories\UserRepository;
use app\usecases\Utility\UtilitiesService;
use Throwable;
use yii\base\ErrorException;

class UtilitiesController extends AppController
{
	private UtilitiesService $service;
	private UserRepository   $userRepository;

	public function __construct(
		$id,
		$module,
		UtilitiesService $service,
		UserRepository $userRepository,
		array $config = []
	)
	{
		$this->service        = $service;
		$this->userRepository = $userRepository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionFixLandObjectPurposes(): SuccessResponse
	{
		$form = new UtilitiesFixPurposesForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		$this->service->fixLandObjectPurposes($form->getDto());

		return $this->success('Назначение успешно исправлено');
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Throwable
	 * @throws ErrorException
	 */
	public function actionTransferCompaniesToConsultant(int $id): SuccessResponse
	{
		$consultant = $this->userRepository->findOneOrThrow($id);

		$form = new UtilitiesTransferCompaniesToConsultantForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		$this->service->transferCompaniesToConsultant($consultant, $form->getDto());

		return $this->success('Компании переведены консультанту');
	}
}
