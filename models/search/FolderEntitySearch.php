<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\ActiveQuery\FolderQuery;
use app\models\Folder;
use app\models\FolderEntity;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class FolderEntitySearch extends Form
{
	public $id;
	public $category;
	public $user_id;

	public function rules(): array
	{
		return [
			[['id', 'user_id'], 'integer'],
			['category', 'string']
		];
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = FolderEntity::find()->distinct()->innerJoinWith(['folder' => function (FolderQuery $q) {
			$q->notDeleted();
		}]);

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => false,
			'sort'       => [
				'enableMultiSort' => true,
				'defaultOrder'    => [
					'sort_order' => SORT_ASC,
				],
				'attributes'      => [
					'id',
					'sort_order',
					'created_at'
				]
			]
		]);

		$this->load($params);

		$this->validateOrThrow();

		$query->andFilterWhere([
			'and',
			[Folder::field('user_id') => $this->user_id],
			[Folder::field('category') => $this->category],
		]);

		return $dataProvider;
	}

}
