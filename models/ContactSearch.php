<?php

namespace app\models;

use app\helpers\SQLHelper;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

/**
 * ContactSearch represents the model behind the search form of `app\models\Contact`.
 */
class ContactSearch extends Form
{
	public $id;
	public $company_id;
	public $type;
	public $first_name;
	public $middle_name;
	public $last_name;
	public $created_at;
	public $updated_at;
	public $consultant_id;
	public $position;
	public $faceToFaceMeeting;
	public $warning;
	public $good;
	public $status;
	public $passive_why;
	public $passive_why_comment;
	public $warning_why_comment;
	public $position_unknown;
	public $isMain;

	public $search;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['company_id', 'type', 'consultant_id', 'position', 'faceToFaceMeeting', 'warning', 'good', 'status', 'passive_why', 'position_unknown', 'isMain'], 'integer'],
			[['id', 'middle_name', 'last_name', 'created_at', 'updated_at', 'first_name', 'passive_why_comment', 'warning_why_comment', 'search'], 'safe'],
		];
	}


	/**
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 * @throws ErrorException
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = Contact::find()
		                ->with(['emails', 'phones', 'websites', 'wayOfInformings', 'consultant.userProfile', 'contactComments.author.userProfile']);

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'pageSize' => 50,
			],
		]);

		$this->load($params);
		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'                => $this->id,
			'company_id'        => $this->company_id,
			'type'              => $this->type,
			'created_at'        => $this->created_at,
			'updated_at'        => $this->updated_at,
			'consultant_id'     => $this->consultant_id,
			'position'          => $this->position,
			'faceToFaceMeeting' => $this->faceToFaceMeeting,
			'warning'           => $this->warning,
			'good'              => $this->good,
			'status'            => $this->status,
			'passive_why'       => $this->passive_why,
			'position_unknown'  => $this->position_unknown,
			'isMain'            => $this->isMain,
		]);

		$query->andFilterWhere(['like', 'middle_name', $this->middle_name])
		      ->andFilterWhere(['like', 'last_name', $this->last_name])
		      ->andFilterWhere(['like', 'first_name', $this->first_name])
		      ->andFilterWhere(['like', 'passive_why_comment', $this->passive_why_comment])
		      ->andFilterWhere(['like', 'warning_why_comment', $this->warning_why_comment]);

		if (!empty($this->search)) {
			$query->andFilterWhere([
				'or',
				[
					'like',
					SQLHelper::concatWithCoalesce([
						Contact::xfield('first_name'),
						Contact::xfield('middle_name'),
						Contact::xfield('last_name')
					]),
					$this->search
				],
				['like', 'id', $this->search],
				['like', 'company_id', $this->search],
				['like', 'passive_why_comment', $this->search],
				['like', 'warning_why_comment', $this->search]

			]);
		}

		return $dataProvider;
	}
}
