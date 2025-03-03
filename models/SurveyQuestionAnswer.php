<?php

namespace app\models;

use app\exceptions\QuestionAnswerConversionException;
use app\helpers\TypeConverterHelper;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\FieldQuery;
use app\models\ActiveQuery\MediaQuery;
use app\models\ActiveQuery\QuestionAnswerQuery;
use app\models\ActiveQuery\RelationQuery;
use app\models\ActiveQuery\SurveyQuery;
use app\models\ActiveQuery\SurveyQuestionAnswerQuery;
use app\models\ActiveQuery\TaskQuery;
use Throwable;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\helpers\Json;

/**
 * This is the model class for table "survey_question_answer".
 *
 * @property int             $id
 * @property int             $survey_id
 * @property int             $question_answer_id
 * @property string|null     $value
 *
 * @property QuestionAnswer  $questionAnswer
 * @property Survey          $survey
 * @property-read Field      $field
 * @property-read Task[]     $tasks
 * @property-read Relation[] $relationSecond
 * @property-read Relation[] $relationFirst
 * @property-read Media[]    $files
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
			[['value'], 'string', 'max' => 1024],
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

	/**
	 * @throws ErrorException
	 */
	public function getRelationSecond(): RelationQuery
	{
		/** @var RelationQuery */
		return $this->morphHasMany(Relation::class, 'id', 'second');
	}

	/**
	 * @throws ErrorException
	 */
	public function getRelationFirst(): RelationQuery
	{
		/** @var RelationQuery */
		return $this->morphHasMany(Relation::class, 'id', 'first');
	}


	/**
	 * @throws ErrorException
	 */
	public function getTasks(): TaskQuery
	{
		/** @var TaskQuery */
		return $this->morphHasManyVia(Task::class, 'id', 'first')
		            ->andOnCondition([Task::field('deleted_at') => null])
		            ->via('relationSecond');
	}

	/**
	 * @throws ErrorException
	 */
	public function getFiles(): MediaQuery
	{
		/** @var MediaQuery */
		return $this->morphHasManyVia(Media::class, 'id', 'second')
		            ->andOnCondition([Media::field('deleted_at') => null])
		            ->via('relationFirst');
	}

	protected function toBool(): bool
	{
		$decoded = Json::decode($this->value);

		return TypeConverterHelper::toBool($decoded);
	}

	/** @return mixed */
	protected function toJSON()
	{
		return Json::decode($this->value);
	}

	protected function toInteger(): int
	{
		$decoded = Json::decode($this->value);

		return TypeConverterHelper::toInt($decoded);
	}

	protected function toString(): string
	{
		$decoded = Json::decode($this->value);

		return TypeConverterHelper::toString($decoded);
	}

	public function hasAnswer(): bool
	{
		return !is_null($this->value);
	}

	public function getMaybeBool(?bool $fallback = false): bool
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
			return $this->toString();
		}

		throw new QuestionAnswerConversionException('string');
	}

	public function getMaybeString(string $fallback = ''): string
	{
		try {
			return $this->toString();
		} catch (Throwable $e) {
			return $fallback;
		}
	}

	/**
	 * @throws QuestionAnswerConversionException
	 */
	public function getInteger(): int
	{
		if ($this->field->canBeConvertedToInteger()) {
			return $this->toInteger();
		}

		throw new QuestionAnswerConversionException('integer');
	}

	public function getMaybeInteger(int $fallback): int
	{
		try {
			return $this->toInteger();
		} catch (Throwable $e) {
			return $fallback;
		}
	}

	/**
	 * @throws Exception
	 */
	public function toEncodedValue()
	{
		if (is_null($this->value)) {
			return null;
		}

		switch ($this->field->type) {
			case Field::TYPE_BOOLEAN:
				return $this->getBool();
			case Field::TYPE_JSON:
				return $this->getJSON();
			case Field::TYPE_STRING:
				return $this->getString();
			case Field::TYPE_INTEGER:
				return $this->getInteger();
			default:
				throw new Exception('Unknown field type');
		}
	}

	/**
	 * @throws Exception
	 */
	public function hasPositiveAnswer(): bool
	{
		if ($this->field->type !== Field::TYPE_BOOLEAN) {
			throw new Exception('SurveyQuestionAnswer cannot be converted to positive/negative answer');
		}

		if ($this->hasAnswer()) {
			return $this->toBool();
		}

		return false;
	}

	/**
	 * @throws Exception
	 */
	public function hasNegativeAnswer(): bool
	{
		if ($this->field->type !== Field::TYPE_BOOLEAN) {
			throw new Exception('SurveyQuestionAnswer cannot be converted to positive/negative answer');
		}

		if ($this->hasAnswer()) {
			return !$this->toBool();
		}

		return false;
	}

	public static function find(): SurveyQuestionAnswerQuery
	{
		return new SurveyQuestionAnswerQuery(get_called_class());
	}
}
