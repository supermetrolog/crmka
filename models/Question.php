<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\QuestionQuery;

/**
 * This is the model class for table "question".
 *
 * @property int              $id
 * @property string           $text
 * @property string           $created_at
 * @property string           $updated_at
 * @property string|null      $deleted_at
 *
 * @property QuestionAnswer[] $answers
 */
class Question extends AR
{
	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;
	protected bool $useSoftCreate = true;

	public static function tableName(): string
	{
		return 'question';
	}

	public function rules(): array
	{
		return [
			[['text'], 'required'],
			[['text'], 'string'],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'         => 'ID',
			'text'       => 'Text',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'deleted_at' => 'Deleted At',
		];
	}

	/**
	 * Gets query for [[QuestionAnswers]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getAnswers()
	{
		return $this->hasMany(QuestionAnswer::className(), ['question_id' => 'id']);
	}

	public static function find(): QuestionQuery
	{
		return new QuestionQuery(get_called_class());
	}
}
