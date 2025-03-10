<?php

namespace app\models;

use app\helpers\ArrayHelper;
use app\kernel\common\models\AR\AR;
use app\kernel\common\models\AR\ManyToManyTrait\ManyToManyTrait;
use app\models\ActiveQuery\EffectQuery;
use app\models\ActiveQuery\FieldQuery;
use app\models\ActiveQuery\QuestionAnswerQuery;
use app\models\ActiveQuery\QuestionQuery;
use app\models\ActiveQuery\SurveyQuestionAnswerQuery;
use Exception;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "question_answer".
 *
 * @property int                  $id
 * @property int                  $question_id
 * @property int                  $field_id
 * @property string               $category
 * @property string               $value
 * @property ?string              $message
 * @property string               $created_at
 * @property string               $updated_at
 * @property string|null          $deleted_at
 *
 * @property Field                $field
 * @property SurveyQuestionAnswer $surveyQuestionAnswer
 * @property-read Effect[]        $effects
 * @property-read Question        $question
 */
class QuestionAnswer extends AR
{
	use ManyToManyTrait;

	public const CATEGORY_YES_NO      = 'yes-no';
	public const CATEGORY_TEXT_ANSWER = 'text-answer';
	public const CATEGORY_TAB         = 'tab';
	public const CATEGORY_CHECKBOX    = 'checkbox';
	public const CATEGORY_FILES       = 'files';
	public const CATEGORY_CUSTOM      = 'custom';

	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;
	protected bool $useSoftCreate = true;

	public static function tableName(): string
	{
		return 'question_answer';
	}

	public function rules(): array
	{
		return [
			[['question_id', 'field_id', 'category'], 'required'],
			[['question_id', 'field_id'], 'integer'],
			['category', 'in', 'range' => self::getCategories()],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
			[['category', 'value'], 'string', 'max' => 255],
			[['message'], 'string', 'max' => 128],
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
			'message'     => 'Message',
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
			self::CATEGORY_FILES,
			self::CATEGORY_CUSTOM,
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

	/**
	 * @return ActiveQuery|SurveyQuestionAnswerQuery
	 */
	public function getSurveyQuestionAnswer(): SurveyQuestionAnswerQuery
	{
		return $this->hasOne(SurveyQuestionAnswer::className(), ['question_answer_id' => 'id']);
	}

	public function getQuestionAnswerEffects(): ActiveQuery
	{
		return $this->hasMany(QuestionAnswerEffect::class, ['question_answer_id' => 'id']);
	}

	public function getEffects(): EffectQuery
	{
		/** @var EffectQuery */
		return $this->hasMany(Effect::class, ['id' => 'effect_id'])->via('questionAnswerEffects');
	}

	/**
	 * @throws Exception
	 */
	public function hasEffectByKind(string $kind): bool
	{
		return ArrayHelper::includesByKey($this->effects, $kind, 'kind');
	}

	public function hasAdditionalMessage(): bool
	{
		return !empty($this->message);
	}

	public function isFilesFieldType(): bool
	{
		return $this->field->field_type === Field::FIELD_TYPE_FILES;
	}

	public static function find(): QuestionAnswerQuery
	{
		return new QuestionAnswerQuery(get_called_class());
	}
}
