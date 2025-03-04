<?php

namespace app\controllers;

use app\exceptions\ValidationErrorHttpException;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\web\http\responses\ErrorResponse;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\Objects;
use app\repositories\ObjectRepository;
use app\usecases\Object\ObjectService;
use InvalidArgumentException;

class UtilitiesController extends AppController
{
	private ObjectService    $objectService;
	private ObjectRepository $objectRepository;

	public function __construct(
		$id,
		$module,
		ObjectService $objectService,
		ObjectRepository $objectRepository,
		array $config = []
	)
	{
		$this->objectService    = $objectService;
		$this->objectRepository = $objectRepository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @return SuccessResponse|ErrorResponse
	 * @throws ModelNotFoundException
	 * @throws ValidationErrorHttpException
	 * @throws SaveModelException
	 */
	public function actionFixLandObjectPurposes()
	{
		$objectId = $this->request->post('object_id');

		if (empty($objectId)) {
			throw new ValidationErrorHttpException('Object id is empty');
		}

		/** @var Objects $object */
		$object = $this->objectRepository->findOneOrThrow((int)$objectId);

		try {
			$this->objectService->fixLandObjectPurposes($object);

			return $this->success();
		} catch (InvalidArgumentException $e) {
			return $this->error($e->getMessage());
		}
	}
}
