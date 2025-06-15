<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\web\http\responses\SuccessResponse;
use app\repositories\CompanyPinnedMessageRepository;
use app\usecases\Company\CompanyPinnedMessageService;
use Throwable;
use yii\base\ErrorException;
use yii\db\StaleObjectException;

class CompanyPinnedMessageController extends AppController
{
	private CompanyPinnedMessageService    $service;
	private CompanyPinnedMessageRepository $repository;

	public function __construct(
		$id,
		$module,
		CompanyPinnedMessageService $service,
		CompanyPinnedMessageRepository $repository,
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

		return new SuccessResponse('Сообщение откреплено от компании');
	}
}