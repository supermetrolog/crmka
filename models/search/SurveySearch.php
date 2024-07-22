<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\Contact;
use app\models\UserProfile;
use yii\data\ActiveDataProvider;
use app\models\Survey;

class SurveySearch extends Form
{
	public $id;
	public $user_id;
	public $contact_id;

	public $user_name;
	public $contact_name;

	public function rules(): array
	{
		return [
			[['id', 'user_id', 'contact_id'], 'integer'],
			[['user_name', 'contact_name'], 'safe'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = Survey::find()
		               ->joinWith('user.userProfile')
		               ->joinWith('contact');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			Survey::field('id')         => $this->id,
			Survey::field('user_id')    => $this->user_id,
			Survey::field('contact_id') => $this->contact_id,
		]);

		$query->orFilterWhere(
			[
				'like',
				'concat(' .
				'coalesce(' . UserProfile::field('first_name') . ', ""), " ", ' .
				'coalesce(' . UserProfile::field('middle_name') . ', ""), " ", ' .
				'coalesce(' . UserProfile::field('last_name') . ', "")' .
				')',
				$this->user_name
			],
		);

		$query->orFilterWhere(
			[
				'like',
				'concat(' .
				'coalesce(' . Contact::field('first_name') . ', ""), " ", ' .
				'coalesce(' . Contact::field('middle_name') . ', ""), " ", ' .
				'coalesce(' . Contact::field('last_name') . ', "")' .
				')',
				$this->contact_name
			],
		);

		return $dataProvider;
	}
}
