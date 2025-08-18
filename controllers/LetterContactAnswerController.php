<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\models\forms\LetterContactAnswer\LetterContactAnswerForm;
use app\resources\Letter\LetterAnswerResource;
use app\usecases\Letter\LetterContactAnswerService;
use Throwable;

class LetterContactAnswerController extends AppController
{
	private LetterContactAnswerService $service;

	public function __construct(
		$id,
		$module,
		LetterContactAnswerService $service,
		array $config = []
	)
	{
		$this->service = $service;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Throwable
	 */
	public function actionCreate(): LetterAnswerResource
	{
		$form = new LetterContactAnswerForm();

		$form->load($this->request->post());

		$form->marked_by_id = $this->user->id;

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new LetterAnswerResource($model);
	}
}
