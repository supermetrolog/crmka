<?php

namespace app\models\search;

use app\enum\Attribute\AttributeInputTypeEnum;
use app\enum\Attribute\AttributeValueTypeEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\Attribute;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class AttributeSearch extends Form
{
	public $id;
	public $kind;
	public $label;
	public $search;
	public $value_type;
	public $input_type;
	public $current_user_id;
	public $created_at;
	public $updated_at;
	public $before;
	public $after;

	public function rules(): array
	{
		return [
			['id', 'integer'],
			[['kind', 'label', 'search', 'value_type', 'input_type'], 'string'],
			[['value_type'], EnumValidator::class, 'enumClass' => AttributeValueTypeEnum::class],
			[['input_type'], EnumValidator::class, 'enumClass' => AttributeInputTypeEnum::class],
			[['current_user_id'], 'integer'],
			[['created_at', 'updated_at', 'before', 'after'], 'safe'],
		];
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = Attribute::find()
		                  ->with(['createdBy.userProfile']);

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'pageSize' => 50,
			],
			'sort'       => [
				'enableMultiSort' => true,
				'defaultOrder'    => [
					'updated_at' => SORT_DESC,
				],
				'attributes'      => [
					'created_at',
					'updated_at' => [
						'asc'  => [
							new Expression(
								'CASE WHEN :current_user_id AND attribute.created_by_id = :current_user_id THEN 0 ELSE 1 END ASC',
								[':current_user_id' => $this->current_user_id]
							),
							'updated_at' => SORT_ASC
						],
						'desc' => [
							new Expression(
								'CASE WHEN :current_user_id AND attribute.created_by_id = :current_user_id THEN 0 ELSE 1 END ASC',
								[':current_user_id' => $this->current_user_id]
							),
							'updated_at' => SORT_DESC
						]
					]
				],
			],
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			Attribute::field('id')         => $this->id,
			Attribute::field('kind')       => $this->kind,
			Attribute::field('value_type') => $this->value_type,
			Attribute::field('input_type') => $this->input_type,
		])->andFilterWhere(['>=', Attribute::field('updated_at'), $this->after])
		      ->andFilterWhere(['<=', Attribute::field('updated_at'), $this->before]);

		if (!empty($this->search)) {
			$query->andFilterWhere([
				'or',
				['like', Attribute::field('kind') => $this->search],
				['like', Attribute::field('description') => $this->search],
			]);
		}

		return $dataProvider;
	}
}