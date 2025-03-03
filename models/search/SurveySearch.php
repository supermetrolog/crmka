<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\Contact;
use app\models\Survey;
use app\models\UserProfile;
use yii\data\ActiveDataProvider;

class SurveySearch extends Form
{
	public $id;
	public $user_id;
	public $contact_id;
	public $chat_member_id;

	public $search;

	public function rules(): array
	{
		return [
			[['id', 'user_id', 'contact_id', 'chat_member_id'], 'integer'],
			[['search'], 'safe'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = Survey::find()
		               ->joinWith('user.userProfile')
		               ->joinWith('contact')
		               ->with(['chatMember']);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			Survey::field('id')             => $this->id,
			Survey::field('user_id')        => $this->user_id,
			Survey::field('contact_id')     => $this->contact_id,
			Survey::field('chat_member_id') => $this->chat_member_id,
		]);

		$query->andFilterWhere([
			'or',
			[
				'like',
				sprintf(
					'concat(coalesce(%s, ""), " ", coalesce(%s, ""), " ", coalesce(%s, ""))',
					UserProfile::field('first_name'),
					UserProfile::field('middle_name'),
					UserProfile::field('last_name'),
				),
				$this->search
			],
			[
				'like',
				sprintf(
					'concat(coalesce(%s, ""), " ", coalesce(%s, ""), " ", coalesce(%s, ""))',
					Contact::field('first_name'),
					Contact::field('middle_name'),
					Contact::field('last_name'),
				),
				$this->search
			],
		]);

		return $dataProvider;
	}
}
