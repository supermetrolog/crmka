<?php

namespace app\models;

use app\helpers\TypeConverterHelper;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\FieldQuery;
use app\models\ActiveQuery\QuestionAnswerQuery;
use app\models\ActiveQuery\SurveyQuery;
use app\models\ActiveQuery\SurveyQuestionAnswerQuery;
use yii\base\Exception;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "survey_question_answer".
 *
 * @property int            $id
 * @property int            $survey_id
 * @property int            $question_answer_id
 * @property string|null    $value
 *
 * @property QuestionAnswer $questionAnswer
 * @property Survey         $survey
 * @property-read Field     $field
 */
class SurveyQuestionAnswer extends AR
{
	public static function tableName(): string
	{
		return 'survey_question_answer';
	}

	public function rules(): array
	{
		return [
			[['survey_id', 'question_answer_id'], 'required'],
			[['survey_id', 'question_answer_id'], 'integer'],
			[['value'], 'string', 'max' => 255],
			[['question_answer_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestionAnswer::className(), 'targetAttribute' => ['question_answer_id' => 'id']],
			[['survey_id'], 'exist', 'skipOnError' => true, 'targetClass' => Survey::className(), 'targetAttribute' => ['survey_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'                 => 'ID',
			'survey_id'          => 'Survey ID',
			'question_answer_id' => 'Question Answer ID',
			'value'              => 'Value',
		];
	}

	/**
	 * @return ActiveQuery|QuestionAnswerQuery
	 */
	public function getQuestionAnswer(): QuestionAnswerQuery
	{
		return $this->hasOne(QuestionAnswer::className(), ['id' => 'question_answer_id']);
	}

	/**
	 * @return ActiveQuery|SurveyQuery
	 */
	public function getSurvey(): SurveyQuery
	{
		return $this->hasOne(Survey::className(), ['id' => 'survey_id']);
	}

	public function getField(): FieldQuery
	{
		/** @var FieldQuery */
		return $this->hasOne(Field::class, ['id' => 'field_id'])->via('questionAnswer');
	}

	protected function toBool(): bool
	{
		return TypeConverterHelper::toBool($this->value);
	}

	public function getMaybeBool(bool $fallback = false): bool
	{
		if ($this->field->canBeConvertedToBool()) {
			return $this->toBool();
		}

		return $fallback;
	}

	/**
	 * @throws Exception
	 */
	public function getBool(): bool
	{
		if ($this->field->canBeConvertedToBool()) {
			return $this->toBool();
		}

		throw new Exception('Answer with this field cannot be converted to bool');
	}


	public static function find(): SurveyQuestionAnswerQuery
	{
		return new SurveyQuestionAnswerQuery(get_called_class());
	}
}
