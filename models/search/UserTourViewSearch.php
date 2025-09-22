<?php

namespace app\models\search;

use app\helpers\SQLHelper;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\User\UserProfile;
use app\models\UserTourView;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class UserTourViewSearch extends Form
{
	public $ids      = [];
	public $user_ids = [];
	public $tour_ids = [];
	public $viewed_before;
	public $viewed_after;
	public $q;


	public function rules(): array
	{
		return [
			[['user_ids', 'ids'], 'each', 'rule' => 'integer'],
			[['tour_ids'], 'each', 'rule' => 'string'],
			['q', 'string'],
			[['viewed_before', 'viewed_after'], 'safe'],
		];
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
		$query = UserTourView::find()->joinWith(['user.userProfile'], false);

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'defaultPageSize' => 30,
				'pageSizeLimit'   => [0, 50],
			],
			'sort'       => [
				'defaultOrder' => [
					'created_at' => SORT_DESC
				],
				'attributes'   => [
					'created_at',
					'steps_viewed',
					'steps_total',
					'user_id',
					'tour_id',
				]
			]
		]);

		$this->load($params);

		$this->validateOrThrow();

		if ($this->hasFilter($this->q)) {
			$query->andFilterWhere([
				'or',
				['like', UserTourView::field('id'), $this->q],
				['like', UserTourView::field('tour_id'), $this->q],
				['like', UserTourView::field('user_id'), $this->q],
				[
					'like',
					SQLHelper::concatWithCoalesce([
						UserProfile::xfield('first_name'),
						UserProfile::xfield('middle_name'),
						UserProfile::xfield('last_name')
					]),
					$this->q
				],
			]);
		}

		$query->andFilterWhere(['>=', UserTourView::field('created_at'), $this->viewed_after])
		      ->andFilterWhere(['<=', UserTourView::field('created_at'), $this->viewed_before])
		      ->andFilterWhere([
			      UserTourView::field('id')       => $this->ids,
			      UserTourView::field('user_ids') => $this->user_ids,
			      UserTourView::field('tour_ids') => $this->tour_ids
		      ]);


		return $dataProvider;
	}
}
