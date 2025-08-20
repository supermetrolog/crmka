<?php

namespace app\models\search;

use app\helpers\SQLHelper;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\Call;
use app\models\Company;
use app\models\Contact;
use app\models\miniModels\Email;
use app\models\miniModels\Phone;
use app\models\UserProfile;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class CallSearch extends Form
{
	public $id;
	public $user_id;
	public $user_ids = [];
	public $contact_id;
	public $created_at;
	public $updated_at;
	public $deleted_at;

	public $status;
	public $statuses = [];
	public $type;

	public $date_start;
	public $date_end;

	public $completed;

	public $search;

	public function rules(): array
	{
		return [
			[['id', 'user_id', 'contact_id', 'status', 'type'], 'integer'],
			['search', 'string'],
			[['created_at', 'updated_at', 'deleted_at', 'date_start', 'date_end'], 'safe'],
			['completed', 'boolean'],
			[['statuses', 'user_ids'], 'each', 'rule' => ['integer']],
		];
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = Call::find()
		             ->joinWith(['user.userProfile', 'contact'])
		             ->with(['contact.websites', 'contact.wayOfInformings', 'contact.consultant.userProfile']);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort'  => [
				'enableMultiSort' => true,
				'defaultOrder'    => [
					'created_at' => SORT_DESC
				],
				'attributes'      => [
					'created_at',
					'status',
					'type',
					'user_name'        => [
						'asc'  => [UserProfile::field('first_name') => SORT_ASC],
						'desc' => [UserProfile::field('first_name') => SORT_DESC]
					],
					'contact_name'     => [
						'asc'  => [Contact::field('first_name') => SORT_ASC],
						'desc' => [Contact::field('first_name') => SORT_DESC]
					],
					'contact_position' => [
						'asc'  => [Contact::field('position_id') => SORT_ASC],
						'desc' => [Contact::field('position_id') => SORT_DESC]
					]
				]
			]
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			Call::field('id')         => $this->id,
			Call::field('user_id')    => $this->user_id,
			Call::field('contact_id') => $this->contact_id,
			Call::field('status')     => $this->status,
			Call::field('type')       => $this->type
		]);

		$query->andFilterWhere([
			Call::field('status')  => $this->statuses,
			Call::field('user_id') => $this->user_ids
		]);

		$query->andFilterWhere([
			'and',
			['>=', Call::field('created_at'), $this->date_start],
			['<=', Call::field('created_at'), $this->date_end]
		]);

		if ($this->isFilterFalse($this->completed)) {
			$query->andFilterWhere(['!=', Call::field('status'), Call::STATUS_COMPLETED]);
		} elseif ($this->isFilterTrue($this->completed)) {
			$query->andFilterWhere([Call::field('status') => Call::STATUS_COMPLETED]);
		}

		if (!empty($this->search)) {
			$query->joinWith(['contact.phones', 'contact.emails', 'contact.company']);

			$query->andFilterWhere([
				'or',
				['like', Call::field('id'), $this->search],
				[
					'like',
					SQLHelper::concatWithCoalesce([
						UserProfile::field('first_name'),
						UserProfile::field('middle_name'),
						UserProfile::field('last_name')
					]),
					$this->search
				],
				[
					'like',
					SQLHelper::concatWithCoalesce([
						Contact::field('first_name'),
						Contact::field('middle_name'),
						Contact::field('last_name')
					]),
					$this->search
				],
				['like', Contact::field('passive_why_comment'), $this->search],
				['like', Contact::field('warning_why_comment'), $this->search],
				['like', Phone::field('phone'), $this->search],
				['like', Email::field('email'), $this->search],
				['like', Call::field('description'), $this->search],
				['like', Company::field('nameEng'), $this->search],
				['like', Company::field('nameRu'), $this->search],
				['like', Company::field('nameBrand'), $this->search],
				['like', Company::field('individual_full_name'), $this->search],
			]);
		} else {
			$query->with(['contact.phones', 'contact.emails', 'contact.company']);
		}

		return $dataProvider;
	}
}