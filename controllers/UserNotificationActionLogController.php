<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ValidateException;
use app\models\search\UserNotificationActionLogSearch;
use app\resources\UserNotification\UserNotificationActionLogSearchResource;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class UserNotificationActionLogController extends AppController
{
	public function __construct(
		$id,
		$module,
		array $config = []
	)
	{
		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new UserNotificationActionLogSearch();

		$dataProvider = $searchModel->search($this->request->get());

		return UserNotificationActionLogSearchResource::fromDataProvider($dataProvider);
	}
}
