<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\Notification\UserNotificationActionLog;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class UserNotificationActionLogSearch extends Form
{
	public $id;
	public $user_id;
	public $executed_after;
	public $executed_before;

	public function rules(): array
	{
		return [
			[['id', 'user_id'], 'integer'],
			[['executed_after', 'executed_before'], 'string']
		];
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = UserNotificationActionLog::find()
		                                  ->distinct()
		                                  ->with(['userNotificationAction', 'user.userProfile'])
		                                  ->with(['userNotification.mailing', 'userNotification.mailing.createdByUser.userProfile', 'userNotification.userNotificationTemplate']);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort'  => [
				'defaultOrder' => [
					'executed_at' => SORT_DESC,
				],
				'attributes'   => [
					'executed_at'
				]
			]
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			UserNotificationActionLog::field('id')      => $this->id,
			UserNotificationActionLog::field('user_id') => $this->user_id,
		]);

		$query->andFilterWhere(['>=', UserNotificationActionLog::field('executed_at'), $this->executed_after])
		      ->andFilterWhere(['<=', UserNotificationActionLog::field('executed_at'), $this->executed_before]);

		return $dataProvider;
	}

}
