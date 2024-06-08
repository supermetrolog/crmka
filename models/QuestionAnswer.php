<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\FieldQuery;
use app\models\ActiveQuery\QuestionAnswerQuery;
use app\models\ActiveQuery\QuestionQuery;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "question_answer".
 *
 * @property int         $id
 * @property int         $question_id
 * @property int         $field_id
 * @property int         $category
 * @property string      $value
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 *
 * @property Field       $field
 */
class QuestionAnswer extends AR
{
	public const CATEGORY_YES_NO      = 1;
	public const CATEGORY_TEXT_ANSWER = 2;
	public const CATEGORY_TAB         = 3;
	public const CATEGORY_CHECKBOX    = 4;

	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;

	public static function tableName(): string
	{
		return 'question_answer';
	}

	public function rules(): array
	{
		return [
			[['question_id', 'field_id', 'category'], 'required'],
			[['question_id', 'field_id', 'category'], 'integer'],
			['category', 'in', 'range' => self::getCategories()],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
			[['value'], 'string', 'max' => 255],
			[['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['question_id' => 'id']],
			[['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => Field::className(), 'targetAttribute' => ['field_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'          => 'ID',
			'question_id' => 'Question ID',
			'field_id'    => 'Field ID',
			'category'    => 'Category',
			'value'       => 'Value',
			'created_at'  => 'Created At',
			'updated_at'  => 'Updated At',
			'deleted_at'  => 'Deleted At',
		];
	}

	public static function getCategories(): array
	{
		return [
			self::CATEGORY_YES_NO,
			self::CATEGORY_TEXT_ANSWER,
			self::CATEGORY_TAB,
			self::CATEGORY_CHECKBOX,
		];
	}

	/**
	 * @return ActiveQuery|QuestionQuery
	 */
	public function getQuestion(): QuestionQuery
	{
		return $this->hasOne(Question::className(), ['id' => 'question_id']);
	}

	/**
	 * @return ActiveQuery|FieldQuery
	 */
	public function getField(): FieldQuery
	{
		return $this->hasOne(Field::className(), ['id' => 'field_id']);
	}

	public static function find(): QuestionAnswerQuery
	{
		return new QuestionAnswerQuery(get_called_class());
	}
}
