<?php

namespace app\models\search;

use app\helpers\SQLHelper;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\User\UserProfile;
use app\models\User\UserTelegramLinkTicket;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class UserTelegramLinkTicketSearch extends Form
{
	public $id;
	public $user_id;
	public $search;

	public function rules(): array
	{
		return [
			[['id', 'user_id'], 'integer'],
			[['search'], 'string']
		];
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = UserTelegramLinkTicket::find()->distinct()->joinWith('user.userProfile');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort'  => [
				'attributes'   => ['created_at'],
				'defaultOrder' => [
					'created_at' => SORT_DESC
				]
			]
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			UserTelegramLinkTicket::field('id')      => $this->id,
			UserTelegramLinkTicket::field('user_id') => $this->user_id
		]);

		$query->andFilterWhere([
			'or',
			[
				'like',
				SQLHelper::concatWithCoalesce([
					UserProfile::field('first_name'),
					UserProfile::field('middle_name'),
					UserProfile::field('last_name')
				]),
				$this->search
			]
		]);

		return $dataProvider;
	}
}
