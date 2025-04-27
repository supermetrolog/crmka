<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\Folder;
use app\models\views\FolderSearchView;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class FolderSearch extends Form
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
		$query = FolderSearchView::find()
		                         ->select([Folder::field('*'), 'entities_count' => 'COUNT(fe.id)'])
		                         ->joinWith(['entities fe'], false)
		                         ->notDeleted()
		                         ->groupBy(Folder::field('id'));

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => false,
			'sort'       => [
				'enableMultiSort' => true,
				'defaultOrder'    => [
					'sort_order' => SORT_ASC,
				],
				'attributes'      => [
					'sort_order',
					'updated_at',
					'created_at',
					'name'
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
