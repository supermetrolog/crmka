<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\QuestionAnswerQuery;
use app\models\ActiveQuery\QuestionQuery;
use app\models\ActiveQuery\RelationQuery;
use app\models\ActiveQuery\SurveyQuery;
use app\models\ActiveQuery\SurveyQuestionAnswerQuery;
use app\models\ActiveQuery\TaskQuery;
use yii\base\ErrorException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "survey".
 *
 * @property int                         $id
 * @property int                         $user_id
 * @property int                         $contact_id
 * @property int                         $chat_member_id
 * @property string                      $created_at
 * @property string                      $updated_at
 *
 * @property Contact                     $contact
 * @property User                        $user
 * @property-read SurveyQuestionAnswer[] $surveyQuestionAnswers
 * @property-read QuestionAnswer[]       $questionAnswers
 * @property-read Question[]             $questions
 * @property-read ChatMember             $chatMember
 * @property-read Task[]                 $tasks
 */
class Survey extends AR
{
	protected bool $useSoftUpdate = true;
	protected bool $useSoftCreate = true;

	public static function tableName(): string
	{
		return 'survey';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'contact_id', 'chat_member_id'], 'required'],
			[['user_id', 'contact_id', 'chat_member_id'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'         => 'ID',
			'user_id'    => 'User ID',
			'contact_id' => 'Contact ID',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getContact(): ActiveQuery
	{
		return $this->hasOne(Contact::className(), ['id' => 'contact_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getUser(): ActiveQuery
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getChatMember(): ActiveQuery
	{
		return $this->hasOne(ChatMember::className(), ['id' => 'chat_member_id']);
	}

	public function getSurveyQuestionAnswers(): SurveyQuestionAnswerQuery
	{
		/** @var SurveyQuestionAnswerQuery */
		return $this->hasMany(SurveyQuestionAnswer::class, ['survey_id' => 'id']);
	}

	public function getQuestionAnswers(): QuestionAnswerQuery
	{
		/** @var QuestionAnswerQuery */
		return $this->hasMany(QuestionAnswer::class, ['id' => 'question_answer_id'])->via('surveyQuestionAnswers');
	}

	public function getQuestions(): QuestionQuery
	{
		/** @var QuestionQuery */
		return $this->hasMany(Question::class, ['id' => 'question_id'])->via('questionAnswers');
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
	public function getTasks(): TaskQuery
	{
		/** @var TaskQuery */
		return $this->morphHasManyVia(Task::class, 'id', 'first')
		            ->andOnCondition([Task::field('deleted_at') => null])
		            ->via('relationSecond');
	}

	public static function find(): SurveyQuery
	{
		return new SurveyQuery(get_called_class());
	}
}
