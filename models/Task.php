<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\kernel\common\models\AR\ManyToManyTrait\ManyToManyTrait;
use app\models\ActiveQuery\MediaQuery;
use app\models\ActiveQuery\RelationQuery;
use app\models\ActiveQuery\TaskCommentQuery;
use app\models\ActiveQuery\TaskHistoryQuery;
use app\models\ActiveQuery\TaskQuery;
use app\models\ActiveQuery\TaskTaskTagQuery;
use yii\base\ErrorException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "task".
 *
 * @property int                $id
 * @property int                $user_id
 * @property string             $message
 * @property int                $status
 * @property string|null        $start
 * @property string|null        $end
 * @property string             $created_by_type
 * @property int                $created_by_id
 * @property string             $created_at
 * @property string             $updated_at
 * @property string             $deleted_at
 * @property string|null        $impossible_to
 *
 * @property User               $user
 * @property User               $createdByUser
 * @property TaskTag[]          $tags
 * @property User               $createdBy
 * @property ChatMemberMessage  $chatMemberMessage
 * @property ChatMember         $chatMember
 * @property TaskComment        $lastComment
 * @property TaskObserver[]     $observers
 * @property TaskObserver       $targetUserObserver
 * @property-read ?TaskHistory  $lastHistory
 * @property-read TaskComment[] $lastComments
 * @property Media[]            $files
 */
class Task extends AR
{
	use ManyToManyTrait;

	public const LAST_COMMENTS_LIMIT = 10;
	public const STATUS_CREATED      = 1;
	public const STATUS_ACCEPTED     = 2;
	public const STATUS_DONE         = 3;
	public const STATUS_IMPOSSIBLE   = 4;


	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;


	public static function tableName(): string
	{
		return 'task';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'message', 'status', 'created_by_type', 'created_by_id'], 'required'],
			[['user_id', 'status', 'created_by_id'], 'integer'],
			[['message'], 'string'],
			[['start', 'end', 'created_at', 'updated_at', 'impossible_to'], 'safe'],
			[['created_by_type'], 'string', 'max' => 255],
			['status', 'in', 'range' => self::getStatuses()],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'              => 'ID',
			'user_id'         => 'User ID',
			'message'         => 'Message',
			'status'          => 'Status',
			'start'           => 'Start',
			'end'             => 'End',
			'created_by_type' => 'Created By Type',
			'created_by_id'   => 'Created By ID',
			'created_at'      => 'Created At',
			'updated_at'      => 'Updated At',
			'impossible_to'   => 'Impossible To'
		];
	}

	public static function getStatuses(): array
	{
		return [
			self::STATUS_CREATED,
			self::STATUS_ACCEPTED,
			self::STATUS_DONE,
			self::STATUS_IMPOSSIBLE,
		];
	}

	/**
	 * Список статусов, на которые можно перевести задачу
	 *
	 * @return int[]
	 */
	public static function getEditableStatuses(): array
	{
		return [
			self::STATUS_ACCEPTED,
			self::STATUS_DONE,
			self::STATUS_IMPOSSIBLE
		];
	}

	public function getUser(): ActiveQuery
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return TaskTaskTagQuery
	 */
	public function getTaskTags(): TaskTaskTagQuery
	{
		/** @var TaskTaskTagQuery $query */
		$query = $this->hasMany(TaskTaskTag::class, ['task_id' => 'id']);

		return $query->notDeleted();
	}

	/**
	 * @return ActiveQuery
	 */
	public function getTags(): ActiveQuery
	{
		return $this->hasMany(TaskTag::class, ['id' => 'task_tag_id'])->via('taskTags');
	}

	/**
	 * @throws ErrorException
	 */
	public function getTagIds(): array
	{
		return $this->getTaskTags()->select('task_tag_id')->column();
	}

	/**
	 * @return ActiveQuery
	 */
	public function getCreatedByUser(): ActiveQuery
	{
		return $this->morphBelongTo(User::class, 'id', 'created_by');
	}

	public function getCreatedBy(): AR
	{
		return $this->createdByUser;
	}


	public static function find(): TaskQuery
	{
		return new TaskQuery(get_called_class());
	}

	/**
	 * @throws ErrorException
	 */
	public function getRelationFirstMany(): RelationQuery
	{
		/** @var RelationQuery */
		return $this->morphHasMany(Relation::class, 'id', 'first');
	}

	/**
	 * @throws ErrorException
	 */
	public function getFiles(): MediaQuery
	{
		/**@var MediaQuery */
		return $this->morphHasManyVia(Media::class, 'id', 'second')
		            ->andOnCondition([Media::field('deleted_at') => null])
		            ->via('relationFirstMany');
	}

	/**
	 * @return ActiveQuery
	 * @throws ErrorException
	 */
	public function getChatMemberMessageRelationSecond(): ActiveQuery
	{
		return $this->morphHasOne(Relation::class, 'id', 'second');
	}

	/**
	 * Test description
	 *
	 * @return ActiveQuery
	 * @throws ErrorException
	 */
	public function getChatMemberMessage(): ActiveQuery
	{
		return $this->morphHasOneVia(ChatMemberMessage::class, 'id', 'first')->via('chatMemberMessageRelationSecond');
	}

	/**
	 * @return ActiveQuery
	 */
	public function getChatMember(): ActiveQuery
	{
		return $this->hasOne(ChatMember::class, ['id' => 'to_chat_member_id'])->via('chatMemberMessage');
	}

	public function getLastComment(): ActiveQuery
	{
		/** @var TaskCommentQuery $query */
		$query = $this->hasOne(TaskComment::class, ['task_id' => 'id']);

		return $query->notDeleted()->orderBy(['id' => SORT_DESC]);
	}

	public function getComments(): TaskCommentQuery
	{
		/** @var TaskCommentQuery $query */
		$query = $this->hasMany(TaskComment::class, ['task_id' => 'id']);

		return $query->notDeleted();
	}

	public function getLastComments(): TaskCommentQuery
	{
		return $this->getComments()->orderBy(['id' => SORT_DESC])->limit(self::LAST_COMMENTS_LIMIT);
	}

	public function getCommentsCount(): int
	{
		return $this->getComments()->count();
	}

	public function getObservers(): ActiveQuery
	{
		return $this->hasMany(TaskObserver::class, ['task_id' => 'id']);
	}

	public function getUserIdsInObservers(): array
	{
		return $this->getObservers()->select('user_id')->column();
	}

	/**
	 * @throws ErrorException
	 */
	public function getFileIds(): array
	{
		return $this->getFiles()->select('id')->column();
	}

	public function getLastHistory(): TaskHistoryQuery
	{
		/** @var TaskHistoryQuery */
		return $this->hasOne(TaskHistory::class, ['task_id' => 'id'])->orderBy(['id' => SORT_DESC]);
	}

	public function getHistoriesCount(): int
	{
		return $this->hasMany(TaskHistory::class, ['task_id' => 'id'])->count();
	}

	public function getTargetUserObserver(): ActiveQuery
	{
		return $this->hasOne(TaskObserver::class, ['user_id' => 'user_id', 'task_id' => 'id']);
	}

	public function isViewed(): bool
	{
		$targetUserObserver = $this->targetUserObserver;

		if ($targetUserObserver === null) {
			return false;
		}

		return $targetUserObserver->viewed_at !== null;
	}

	public function getViewedAt(): ?string
	{
		$targetUserObserver = $this->targetUserObserver;

		if ($targetUserObserver === null) {
			return null;
		}

		return $targetUserObserver->viewed_at;
	}

	public function canBeReassigned(): bool
	{
		return $this->status !== self::STATUS_DONE && !$this->isDeleted();
	}

	public function canBeRestored(): bool
	{
		return $this->isDeleted();
	}

	/**
	 * @throws ErrorException
	 */
	public function getFilesCount(): int
	{
		return $this->getFiles()->count();
	}
}
