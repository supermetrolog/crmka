<?php

namespace app\models\search;

use app\helpers\DateTimeHelper;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\Notification\NotificationChannel;
use app\models\Notification\UserNotification;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class UserNotificationSearch extends Form
{
	public $id;
	public $user_id;
	public $channel;
	public $since;
	public $acted;
	public $expired;
	public $processed;

	public ?int $id_less_then = null;

	public int $limit = 20;

	public bool $paginated = false;

	public function rules(): array
	{
		return [
			[['id', 'user_id', 'limit', 'id_less_then'], 'integer'],
			[['channel'], 'string'],
			[['since'], 'safe'],
			[['acted', 'expired', 'processed', 'paginated'], 'boolean']
		];
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = UserNotification::find()
		                         ->distinct()
		                         ->innerJoinWith(['mailing'])
		                         ->with(['mailing.createdByUser.userProfile']);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort'  => [
				'defaultOrder' => [
					'created_at' => SORT_DESC,
				],
				'attributes'   => [
					'created_at'
				]
			]
		]);

		$this->load($params);

		$this->validateOrThrow();

		if (!$this->paginated) {
			$query->limit($this->limit)->andFilterWhere(['<', UserNotification::field('id'), $this->id_less_then]);
			$dataProvider->setPagination(false);
		}

		$query->andFilterWhere([
			UserNotification::field('id')      => $this->id,
			UserNotification::field('user_id') => $this->user_id,
		]);


		$query->andFilterWhere(['>', UserNotification::field('created_at'), $this->since])
		      ->andFilterWhere(['<', UserNotification::field('created_at'), $this->since]);

		if ($this->hasFilter($this->channel)) {
			$query->innerJoinWith(['mailing.channel'], false);

			$query->andFilterWhere([NotificationChannel::field('slug') => $this->channel]);
		}

		if ($this->isFilterTrue($this->acted)) {
			$query->andWhereNotNull(UserNotification::field('acted_at'));
		}

		if ($this->isFilterFalse($this->acted)) {
			$query->andWhereNull(UserNotification::field('acted_at'));
		}

		if ($this->isFilterTrue($this->expired)) {
			$query->andWhereNotNull(UserNotification::field('expired_at'))
			      ->andWhere(['<', UserNotification::field('expired_at'), DateTimeHelper::nowf()]);
		}

		if ($this->isFilterFalse($this->expired)) {
			$query->andWhere([
				'or',
				[UserNotification::field('expires_at') => null],
				['>', UserNotification::field('expires_at'), DateTimeHelper::nowf()]
			]);
		}

		if ($this->isFilterTrue($this->processed)) {
			$query->andWhere([
				'or',
				['is not', UserNotification::field('acted_at'), null],
				[
					'and',
					['is not', UserNotification::field('expires_at'), null],
					['<', UserNotification::field('expires_at'), DateTimeHelper::nowf()],
				]
			]);
		}

		return $dataProvider;
	}

}
