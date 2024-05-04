<?php

namespace app\controllers\ChatMember;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\resources\JsonResource;
use app\models\ChatMember;
use app\models\search\ChatMemberSearch;
use app\resources\ChatMember\ChatMemberFullResource;
use app\resources\ChatMember\ChatMemberResource;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class ChatMemberController extends AppController
{
	/**
	 * @throws ValidateException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		// TODO: Сделать разные поиски для разны типов моделей так как они будут сильно отличаться!

		$searchModel = new ChatMemberSearch();

		$dataProvider = $searchModel->search($this->request->get());

		return ChatMemberResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): JsonResource
	{
		return ChatMemberFullResource::make($this->findModel($id));
	}

	/**
	 * @throws NotFoundHttpException
	 */
	protected function findModel(int $id): ?ChatMember
	{
		// TODO: add in generator

		$model = ChatMember::find()
		                   ->byId($id)
//		                   ->with(['messages' => function (ChatMemberMessageQuery $query) {
//			                   $query->notDeleted();
//		                   }])
                           ->with(['objectChatMember.object.company'])
		                   ->with([
			                   'request.company',
			                   'request.regions',
			                   'request.directions',
			                   'request.districts',
			                   'request.objectTypes',
			                   'request.objectClasses',
		                   ])
		                   ->with(['user.userProfile'])
		                   ->one();

		if ($model) {
			return $model;
		}

		throw new NotFoundHttpException('The requested model does not exist.');
	}
}
