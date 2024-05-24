<?php

namespace app\controllers;

use app\exceptions\domain\model\SaveModelException;
use app\exceptions\domain\model\ValidateException;
use app\kernel\common\controller\AppController;
use app\models\search\ReminderSearch;
use app\models\Reminder;
use app\repositories\ReminderRepository;
use app\usecases\Reminder\CreateReminderService;
use app\usecases\Reminder\ReminderService;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class ReminderController extends AppController
{
	private ReminderService       $service;
	private CreateReminderService $createAlertService;
	private ReminderRepository    $repository;


	public function __construct(
		$id,
		$module,
		ReminderService $service,
		CreateReminderService $createAlertService,
		ReminderRepository $repository,
		array $config = []
	)
	{
		$this->service            = $service;
		$this->createAlertService = $createAlertService;
		$this->repository         = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 */
    public function actionIndex(): ActiveDataProvider
    {
        $searchModel = new ReminderSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }

	/**
	 * @throws NotFoundHttpException
	 */
    public function actionView(int $id): Reminder
    {
		return $this->findModel($id);
    }

	/**
	 * @throws SaveModelException
	 */
    public function actionCreate(): Reminder    {
        $model = new Reminder();

		$model->load(Yii::$app->request->post());
		$model->saveOrThrow();

		return $model;
    }

	/**
	 * @throws SaveModelException
	 * @throws NotFoundHttpException
	 */
    public function actionUpdate(int $id): Reminder    {
		$model = $this->findModel($id);

		$model->load(Yii::$app->request->post());
		$model->saveOrThrow();

		return $model;
    }

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws NotFoundHttpException
	 */
    public function actionDelete(int $id): void
    {
		$this->findModel($id)->delete();
    }


	/**
	 * @throws NotFoundHttpException
	 */
    protected function findModel(int $id): ?Reminder
    {
		if (($model = Reminder::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
    }
}
