<?php

namespace app\models\letter;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use yii\data\ActiveDataProvider;

class LetterSearch extends Form
{
	public $id;
	public $user_id;
	public $status;
	public $type;
	public $shipping_method;
	public $subject;
	public $body;

	public function rules(): array
	{
		return [
			[['id', 'user_id', 'status', 'type', 'shipping_method'], 'integer'],
			[['subject', 'body', 'created_at'], 'safe'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		// EXPAND = letterContacts.email,letterEmails.email,letterPhones.phone,letterOffers.offer, letterContacts.phone,user.userProfile,letterWays
		$query = Letter::find()->with(['letterContacts.email', 'letterEmails.email', 'letterPhones.phone', 'letterOffers.offer', 'letterContacts.phone', 'user.userProfile', 'letterWays']);

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'defaultPageSize' => 50,
				'pageSizeLimit'   => [0, 50],
			],
			'sort'       => [
				'enableMultiSort' => true,
				'defaultOrder'    => [
					'created_at' => SORT_DESC
				],
				'attributes'      => [
					'created_at',
					'status'
				],
			]
		]);

		$this->load($params);
		$this->validateOrThrow();

		$query->andFilterWhere([
			'id'              => $this->id,
			'user_id'         => $this->user_id,
			'status'          => $this->status,
			'type'            => $this->type,
			'shipping_method' => $this->shipping_method,
		]);

		$query->andFilterWhere(['like', 'subject', $this->subject])
		      ->andFilterWhere(['like', 'body', $this->body]);

		return $dataProvider;
	}
}
