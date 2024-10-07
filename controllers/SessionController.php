<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\search\UserAccessTokenSearch;
use app\models\UserAccessToken;
use app\resources\User\UserAccessTokenResource;
use app\usecases\User\UserAccessTokenService;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\ForbiddenHttpException;

class SessionController extends AppController
{
	private UserAccessTokenService $accessTokenService;


	public function __construct(
		$id,
		$module,
		UserAccessTokenService $accessTokenService,
		array $config = []
	)
	{
		$this->accessTokenService = $accessTokenService;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @return ActiveDataProvider
	 * @throws ValidateException
	 * @throws ForbiddenHttpException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$identity = $this->user->identity;
		if (!$identity->isAdministrator() && !$identity->isDirector()) {
			throw new ForbiddenHttpException('У вас нет прав на просмотр сессий пользователей.');
		}

		$searchModel = new UserAccessTokenSearch();

		$dataProvider = $searchModel->search($this->request->get());

		return UserAccessTokenResource::fromDataProvider($dataProvider);
	}

	/**
	 * @param int $id
	 *
	 * @return SuccessResponse
	 * @throws ForbiddenHttpException
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$user  = $this->user->identity;
		$model = $this->findModel($id);

		if ($model->user_id !== $user->id && !$user->isAdministrator() && !$user->isDirector()) {
			throw new ForbiddenHttpException('У вас нет прав на удаление сессии данного пользователя');
		}

		$this->accessTokenService->delete($model);

		return new SuccessResponse('Сессия пользователя успешно удалена');
	}

	/**
	 * @param int $id
	 *
	 * @return UserAccessToken
	 * @throws ModelNotFoundException
	 */
	protected function findModel(int $id): UserAccessToken
	{
		return UserAccessToken::find()->byId($id)->oneOrThrow();
	}
}
