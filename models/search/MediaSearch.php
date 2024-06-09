<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use yii\data\ActiveDataProvider;
use app\models\Media;

class MediaSearch extends Form
{
	public $id;
	public $name;
	public $original_name;
	public $extension;
	public $path;
	public $category;
	public $model_type;
	public $model_id;
	public $created_at;
	public $deleted;
	public $mime_type;

	public function rules(): array
	{
		return [
			[['id', 'model_id'], 'integer'],
			[['deleted'], 'boolean'],
			[['name', 'original_name', 'extension', 'path', 'category', 'model_type', 'created_at', 'mime_type'], 'safe'],
		];
	}

	/**
	 * @throws ValidateException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = Media::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->validateOrThrow();

		if ($this->isFilterTrue($this->deleted)) {
			$query->deleted();
		}

		if ($this->isFilterFalse($this->deleted)) {
			$query->notDeleted();
		}

		$query->andFilterWhere([
			'id'         => $this->id,
			'model_id'   => $this->model_id,
			'created_at' => $this->created_at,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
		      ->andFilterWhere(['like', 'original_name', $this->original_name])
		      ->andFilterWhere(['like', 'extension', $this->extension])
		      ->andFilterWhere(['like', 'path', $this->path])
		      ->andFilterWhere(['like', 'category', $this->category])
		      ->andFilterWhere(['like', 'model_type', $this->model_type])
		      ->andFilterWhere(['like', 'mime_type', $this->mime_type]);

		return $dataProvider;
	}
}
