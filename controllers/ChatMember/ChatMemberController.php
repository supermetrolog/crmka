<?php

namespace app\controllers\ChatMember;

use app\exceptions\domain\model\ValidateException;
use app\kernel\common\controller\AppController;
use app\models\ChatMember;
use app\models\search\ChatMemberSearch;
use app\resources\ChatMember\ChatMemberResource;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class ChatMemberController extends AppController
{
	/**
	 * @throws ValidateException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel  = new ChatMemberSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return ChatMemberResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): ChatMemberResource
	{
		return new ChatMemberResource($this->findModel($id));
	}

	/**
	 * @throws NotFoundHttpException
	 */
	protected function findModel(int $id): ?ChatMember
	{
		if (($model = ChatMember::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
