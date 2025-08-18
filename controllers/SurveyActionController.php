<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\models\forms\Survey\SurveyForm;
use app\models\forms\SurveyAction\SurveyActionForm;
use app\repositories\SurveyActionRepository;
use app\resources\Survey\SurveyActionResource;
use app\usecases\SurveyAction\SurveyActionService;
use Exception;

class SurveyActionController extends AppController
{
	private SurveyActionService    $service;
	private SurveyActionRepository $repository;

	public function __construct(
		$id,
		$module,
		SurveyActionService $service,
		SurveyActionRepository $repository,
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
	 * @throws Exception
	 */
	public function actionUpdate(int $id): SurveyActionResource
	{
		$model = $this->repository->findOneOrThrow($id);

		$form = new SurveyActionForm();

		$form->setScenario(SurveyForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new SurveyActionResource($model);
	}
}
