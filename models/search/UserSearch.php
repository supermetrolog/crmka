<?php

namespace app\models\search;

use app\helpers\ArrayHelper;
use app\helpers\SQLHelper;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\User;
use app\models\UserProfile;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class UserSearch extends Form
{
	public $id;
	public $username;
	public $email;
	public $role;
	public $user_id_old;
	public $email_username;
	public $search;

	/** @var int|int[]| $status */
	public $status = User::STATUS_ACTIVE;

	public function rules(): array
	{
		return [
			[['user_id_old', 'status'], 'integer'],
			[['username', 'search', 'email', 'email_username'], 'string'],
			[
				'role',
				'each',
				'rule' => ['in', 'range' => User::getRoles()]
			]
		];
	}

	public function load($data, $formName = null): bool
	{
		if (isset($data['role'])) {
			$data['role'] = ArrayHelper::toArray($data['role']);
		}

		return parent::load($data, $formName);
	}

	/**
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = User::find()->joinWith(['userProfile' => function ($query) {
			$query->with(['emails', 'phones']);
		}]);

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => false,
			'sort'       => [
				'enableMultiSort' => true,
				'defaultOrder'    => [
					'updated_at' => SORT_DESC
				],
				'attributes'      => [
					'role',
					'updated_at',
					'created_at',
					'middle_name' => [
						'asc'  => [UserProfile::field('middle_name') => SORT_ASC],
						'desc' => [UserProfile::field('middle_name') => SORT_DESC]
					]
				]
			]
		]);

		$this->load($params);
		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'          => $this->id,
			'role'        => $this->role,
			'user_id_old' => $this->user_id_old,
			'status'      => $this->status
		]);

		if (!isset($this->search)) {
			$query->andFilterWhere([
				'or',
				['like', 'id', $this->search],
				['like', 'username', $this->search],
				['like', 'email', $this->search],
				['like', 'email_username', $this->search],
				['like', 'user_id_old', $this->search],
				[
					'like',
					SQLHelper::concatWithCoalesce([
						UserProfile::xfield('first_name'),
						UserProfile::xfield('middle_name'),
						UserProfile::xfield('last_name')
					]),
					$this->search
				],
			]);
		}

		return $dataProvider;
	}
}
