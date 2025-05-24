<?php

namespace app\models\search;

use app\helpers\SQLHelper;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ChatMember;
use app\models\Company;
use app\models\SurveyDraft;
use app\models\UserProfile;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class SurveyDraftSearch extends Form
{
	public $id;
	public $user_id;
	public $chat_member_id;
	public $company_id;

	public $search;

	public function rules(): array
	{
		return [
			[['id', 'user_id', 'chat_member_id'], 'integer'],
			[['search'], 'safe'],
		];
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = SurveyDraft::find()
		                    ->joinWith(['chatMember', 'user.userProfile']);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			SurveyDraft::field('id')             => $this->id,
			SurveyDraft::field('user_id')        => $this->user_id,
			SurveyDraft::field('chat_member_id') => $this->chat_member_id,
			ChatMember::field('model_id')        => $this->company_id
		]);

		if ($this->hasFilter($this->search)) {
			$query->andFilterWhere([
				'or',
				[SurveyDraft::field('id') => $this->search],
				[SurveyDraft::field('user_id') => $this->search],
				[SurveyDraft::field('chat_member_id') => $this->search],
				[ChatMember::field('model_id') => $this->search],
				[Company::field('nameEng') => $this->search],
				[Company::field('nameRu') => $this->search],
				[Company::field('nameBrand') => $this->search],
				[Company::field('individual_full_name') => $this->search],
				[
					'like',
					SQLHelper::concatWithCoalesce([
						UserProfile::field('first_name'),
						UserProfile::field('middle_name'),
						UserProfile::field('last_name'),
					]),
					$this->search
				]
			]);
		}

		return $dataProvider;
	}
}
