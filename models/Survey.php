<?php

namespace app\models;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\CallQuery;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\QuestionAnswerQuery;
use app\models\ActiveQuery\QuestionQuery;
use app\models\ActiveQuery\RelationQuery;
use app\models\ActiveQuery\SurveyQuery;
use app\models\ActiveQuery\SurveyQuestionAnswerQuery;
use app\models\ActiveQuery\TaskQuery;
use app\models\ActiveQuery\TaskRelationEntityQuery;
use Exception;
use yii\base\ErrorException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "survey".
 *
 * @property int                         $id
 * @property int                         $user_id
 * @property int                         $contact_id
 * @property int                         $chat_member_id
 * @property ?int                        $related_survey_id
 * @property string                      $status
 * @property string                      $type
 * @property ?string                     $comment
 * @property string                      $created_at
 * @property string                      $updated_at
 * @property string                      $deleted_at
 * @property string                      $completed_at
 *
 * @property-read ?Contact               $contact
 * @property-read User                   $user
 * @property-read SurveyQuestionAnswer[] $surveyQuestionAnswers
 * @property-read QuestionAnswer[]       $questionAnswers
 * @property-read Question[]             $questions
 * @property-read ChatMember             $chatMember
 * @property-read Task[]                 $tasks
 * @property-read ?Survey                $relatedSurvey
 * @property-read Survey[]               $dependentSurveys
 * @property-read Call[]                 $calls
 * @property-read ChatMemberMessage      $chatMemberMessage
 * @property-read ?Call                  $mainCall
 */
class Survey extends AR
{
	protected bool $useSoftUpdate = true;
	protected bool $useSoftCreate = true;
	protected bool $useSoftDelete = true;

	public const STATUS_DRAFT     = 'draft';
	public const STATUS_COMPLETED = 'completed';
	public const STATUS_CANCELED  = 'canceled';
	public const STATUS_DELAYED   = 'delayed';

	public static function getStatuses(): array
	{
		return [
			self::STATUS_DRAFT,
			self::STATUS_COMPLETED,
			self::STATUS_CANCELED,
			self::STATUS_DELAYED
		];
	}

	public const TYPE_BASIC    = 'basic';
	public const TYPE_ADVANCED = 'advanced';

	public static function getTypes(): array
	{
		return [
			self::TYPE_BASIC,
			self::TYPE_ADVANCED
		];
	}

	public static function tableName(): string
	{
		return 'survey';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'chat_member_id'], 'required'],
			[['user_id', 'contact_id', 'chat_member_id', 'related_survey_id'], 'integer'],
			[['status', 'type'], 'string', 'max' => 16],
			[['comment'], 'string', 'max' => 1024],
			['status', 'in', 'range' => self::getStatuses()],
			['type', 'in', 'range' => self::getTypes()],
			[['created_at', 'updated_at', 'completed_at', 'deleted_at'], 'safe'],
			[['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
			[['related_survey_id'], 'exist', 'skipOnError' => true, 'targetClass' => Survey::class, 'targetAttribute' => ['related_survey_id' => 'id']]
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
	public function getRelationFirst(): RelationQuery
	{
		/** @var RelationQuery */
		return $this->morphHasMany(Relation::class, 'id', 'first');
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
	public function getTaskRelationEntities(): TaskRelationEntityQuery
	{
		/** @var TaskRelationEntityQuery */
		return $this->morphHasMany(TaskRelationEntity::class, 'id', 'entity')
		            ->andOnCondition([TaskRelationEntity::field('deleted_at') => null]);
	}

	/**
	 * @throws ErrorException
	 */
	public function getTasks(): TaskQuery
	{
		/** @var TaskQuery */
		return $this->hasMany(Task::class, ['id' => 'task_id'])->via('taskRelationEntities')->andOnCondition([Task::field('deleted_at') => null]);
	}

	/**
	 * @throws Exception
	 */
	public function getSurveyQuestionAnswerByEffectKind(string $effectKind): ?SurveyQuestionAnswer
	{
		return $this->getSurveyQuestionAnswers()->innerJoinWith(['questionAnswer.effects' => function (AQ $query) use ($effectKind) {
			return $query->andWhere([Effect::field('kind') => $effectKind]);
		}])->one();
	}

	public function getRelatedSurvey(): SurveyQuery
	{
		/** @var SurveyQuery */
		return $this->hasOne(Survey::class, ['id' => 'related_survey_id']);
	}

	public function getDependentSurveys(): SurveyQuery
	{
		/** @var SurveyQuery */
		return $this->hasMany(Survey::class, ['related_survey_id' => 'id']);
	}

	/**
	 * @throws ErrorException
	 */
	public function getCalls(): CallQuery
	{
		/** @var CallQuery */
		return $this->morphHasManyVia(Call::class, 'id', 'second')
		            ->via('relationFirst');
	}

	/**
	 * @throws ErrorException
	 */
	public function getMainCall(): ?CallQuery
	{
		/** @var CallQuery */
		return $this->morphHasOneVia(Call::class, 'id', 'second')
		            ->andOnCondition([Call::field('contact_id') => $this->contact_id])
		            ->via('relationFirst');
	}

	/**
	 * @throws ErrorException
	 */
	public function getChatMemberMessage(): ChatMemberMessageQuery
	{
		/** @var ChatMemberMessageQuery */
		return $this->morphHasOneVia(ChatMemberMessage::class, 'id', 'first')->via('relationSecond');
	}

	public static function find(): SurveyQuery
	{
		return new SurveyQuery(static::class);
	}

	public function isDraft(): bool
	{
		return $this->status === self::STATUS_DRAFT;
	}

	public function isCompleted(): bool
	{
		return $this->status === self::STATUS_COMPLETED;
	}

	public function isCanceled(): bool
	{
		return $this->status === self::STATUS_CANCELED;
	}

	public function isDelayed(): bool
	{
		return $this->status === self::STATUS_DELAYED;
	}

	public function isAdvanced(): bool
	{
		return $this->type === self::TYPE_ADVANCED;
	}

	public function isBasic(): bool
	{
		return $this->type === self::TYPE_BASIC;
	}
}
