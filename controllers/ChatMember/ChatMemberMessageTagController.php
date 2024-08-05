<?php

namespace app\controllers\ChatMember;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessageTag;
use app\models\search\ChatMemberMessageTagSearch;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class ChatMemberMessageTagController extends AppController
{
	/**
	 * @return ActiveDataProvider
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new ChatMemberMessageTagSearch();

		return $searchModel->search(Yii::$app->request->queryParams);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): ChatMemberMessageTag
	{
		return $this->findModel($id);
	}

	/**
	 * @return ChatMemberMessageTag
	 * @throws SaveModelException
	 */
	public function actionCreate(): ChatMemberMessageTag
	{
		$model = new ChatMemberMessageTag();

		$model->load(Yii::$app->request->post());
		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 * @throws NotFoundHttpException
	 */
	public function actionUpdate(int $id): ChatMemberMessageTag
	{
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
	protected function findModel(int $id): ChatMemberMessageTag
	{
		if (($model = ChatMemberMessageTag::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
