<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\User\User;
use app\models\User\UserAccessToken;
use yii\data\ActiveDataProvider;

class UserAccessTokenSearch extends Form
{
	public $id;
	public $user_ids;

	public function rules(): array
	{
		return [
			[['id'], 'integer'],
			['user_ids', 'each', 'rule' => [
				'exist',
				'targetClass'     => User::class,
				'targetAttribute' => ['user_ids' => 'id'],
			]],
		];
	}

	/**
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = UserAccessToken::find()->valid();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort'  => [
				'defaultOrder' => [
					'created_at' => SORT_DESC,
				],
				'attributes'   => [
					'created_at',
					'expires_at',
					'user_id'
				]
			]
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'      => $this->id,
			'user_id' => $this->user_ids
		]);

		return $dataProvider;
	}
}
