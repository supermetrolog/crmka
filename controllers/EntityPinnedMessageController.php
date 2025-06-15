<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\web\http\responses\SuccessResponse;
use app\repositories\EntityPinnedMessageRepository;
use app\usecases\EntityPinnedMessage\EntityPinnedMessageService;
use Throwable;
use yii\base\ErrorException;
use yii\db\StaleObjectException;

class EntityPinnedMessageController extends AppController
{
	private EntityPinnedMessageService    $service;
	private EntityPinnedMessageRepository $repository;

	public function __construct(
		$id,
		$module,
		EntityPinnedMessageService $service,
		EntityPinnedMessageRepository $repository,
		array $config = []
	)
	{
		$this->service    = $service;
		$this->repository = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ErrorException
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function actionDelete($id): SuccessResponse
	{
		$comment = $this->repository->findOneOrThrow($id);

		$this->service->delete($comment);

		return new SuccessResponse('Сообщение откреплено');
	}
}