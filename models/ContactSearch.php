<?php

namespace app\models;

use app\helpers\SQLHelper;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\miniModels\ContactComment;
use app\models\miniModels\Email;
use app\models\miniModels\Phone;
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
			Contact::field('id')                => $this->id,
			Contact::field('company_id')        => $this->company_id,
			Contact::field('type')              => $this->type,
			Contact::field('created_at')        => $this->created_at,
			Contact::field('updated_at')        => $this->updated_at,
			Contact::field('consultant_id')     => $this->consultant_id,
			Contact::field('position')          => $this->position,
			Contact::field('faceToFaceMeeting') => $this->faceToFaceMeeting,
			Contact::field('warning')           => $this->warning,
			Contact::field('good')              => $this->good,
			Contact::field('status')            => $this->status,
			Contact::field('passive_why')       => $this->passive_why,
			Contact::field('position_unknown')  => $this->position_unknown,
			Contact::field('isMain')            => $this->isMain,
		]);

		$query->andFilterWhere(['like', Contact::field('middle_name'), $this->middle_name])
		      ->andFilterWhere(['like', Contact::field('last_name'), $this->last_name])
		      ->andFilterWhere(['like', Contact::field('first_name'), $this->first_name])
		      ->andFilterWhere(['like', Contact::field('passive_why_comment'), $this->passive_why_comment])
		      ->andFilterWhere(['like', Contact::field('warning_why_comment'), $this->warning_why_comment]);

		if (!empty($this->search)) {
			$query->joinWith(['emails', 'phones', 'contactComments', 'company']);

			$query->andFilterWhere([
				'or',
				[
					'like',
					SQLHelper::concatWithCoalesce([
						Contact::field('first_name'),
						Contact::field('middle_name'),
						Contact::field('last_name')
					]),
					$this->search
				],
				['like', Contact::field('id'), $this->search],
				['like', Contact::field('company_id'), $this->search],
				['like', Contact::field('passive_why_comment'), $this->search],
				['like', Contact::field('warning_why_comment'), $this->search],
				['like', Phone::field('phone'), $this->search],
				['like', Email::field('email'), $this->search],
				['like', ContactComment::field('comment'), $this->search],
				['like', Company::field('nameEng'), $this->search],
				['like', Company::field('nameRu'), $this->search],
				['like', Company::field('nameBrand'), $this->search],
				['like', Company::field('individual_full_name'), $this->search],
			]);
		}

		return $dataProvider;
	}
}
