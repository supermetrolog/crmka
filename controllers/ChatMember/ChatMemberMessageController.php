<?php

namespace app\controllers\ChatMember;

use app\exceptions\domain\model\SaveModelException;
use app\exceptions\domain\model\ValidateException;
use app\kernel\common\controller\AppController;
use app\models\ChatMemberMessage;
use app\models\search\ChatMemberMessageSearch;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class ChatMemberMessageController extends AppController
{
	/**
	 * @throws ValidateException
	 */
    public function actionIndex(): ActiveDataProvider
    {
        $searchModel = new ChatMemberMessageSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }

	/**
	 * @throws NotFoundHttpException
	 */
    public function actionView(int $id): ChatMemberMessage
    {
		return $this->findModel($id);
    }

	/**
	 * @throws SaveModelException
	 */
    public function actionCreate(): ChatMemberMessage    {
        $model = new ChatMemberMessage();

		$model->load(Yii::$app->request->post());
		$model->saveOrThrow();

		return $model;
    }

	/**
	 * @throws SaveModelException
	 * @throws NotFoundHttpException
	 */
    public function actionUpdate(int $id): ChatMemberMessage    {
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
    protected function findModel(int $id): ?ChatMemberMessage
    {
		if (($model = ChatMemberMessage::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
    }
}
