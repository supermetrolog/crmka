<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use yii\data\ActiveDataProvider;
use app\models\Question;

class QuestionSearch extends Form
{
	public $id;
	public $text;
	public $deleted;
	
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['text'], 'safe'],
            [['deleted'], 'boolean'],
        ];
    }

	/**
	 * @throws ValidateException
	 */
    public function search(array $params): ActiveDataProvider
    {
        $query = Question::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		$this->validateOrThrow();

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text]);

	    if ($this->isFilterTrue($this->deleted)) {
		    $query->deleted();
	    }

	    if ($this->isFilterFalse($this->deleted)) {
		    $query->notDeleted();
	    }

        return $dataProvider;
    }
}
