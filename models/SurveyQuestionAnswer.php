<?php

namespace app\models;

use app\exceptions\QuestionAnswerConversionException;
use app\helpers\TypeConverterHelper;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\FieldQuery;
use app\models\ActiveQuery\QuestionAnswerQuery;
use app\models\ActiveQuery\SurveyQuery;
use app\models\ActiveQuery\SurveyQuestionAnswerQuery;
use Throwable;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\helpers\Json;

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

	/** @return mixed */
	protected function toJSON()
	{
		return Json::decode($this->value);
	}


	public function getMaybeBool(bool $fallback = false): bool
	{
		try {
			return $this->toBool();
		} catch (Throwable $e) {
			return $fallback;
		}
	}

	/**
	 * @throws Exception
	 */
	public function getBool(): bool
	{
		if ($this->field->canBeConvertedToBool()) {
			return $this->toBool();
		}

		throw new QuestionAnswerConversionException('bool');
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	public function getJSON()
	{
		if ($this->field->canBeConvertedToJSON()) {
			return $this->toJSON();
		}

		throw new QuestionAnswerConversionException('json');
	}

	/**
	 * @throws Exception
	 */
	public function getString(): string
	{
		if ($this->field->canBeConvertedToString()) {
			return $this->value;
		}

		throw new QuestionAnswerConversionException('string');
	}

	public function getMaybeString(string $fallback = ''): string
	{
		try {
			return $this->getString();
		} catch (Throwable $e) {
			return $fallback;
		}
	}


	public static function find(): SurveyQuestionAnswerQuery
	{
		return new SurveyQuestionAnswerQuery(get_called_class());
	}
}
