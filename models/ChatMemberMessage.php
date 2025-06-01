<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\AlertQuery;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\ChatMemberMessageTagQuery;
use app\models\ActiveQuery\ChatMemberMessageViewQuery;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ActiveQuery\MediaQuery;
use app\models\ActiveQuery\RelationQuery;
use app\models\ActiveQuery\ReminderQuery;
use app\models\ActiveQuery\SurveyQuery;
use app\models\ActiveQuery\TaskQuery;
use app\models\ActiveQuery\UserNotificationQuery;
use app\models\Notification\UserNotification;
use yii\base\ErrorException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "chat_member_message".
 *
 * @property int                     $id
 * @property int                     $to_chat_member_id
 * @property int|null                $from_chat_member_id
 * @property int|null                $reply_to_id
 * @property string|null             $message
 * @property ?string                 $template
 * @property string                  $created_at
 * @property string                  $updated_at
 * @property string                  $deleted_at
 *
 * @property ChatMember              $fromChatMember
 * @property ChatMember              $toChatMember
 * @property Task[]                  $tasks
 * @property Alert[]                 $alerts
 * @property Contact[]               $contacts
 * @property UserNotification[]      $notifications
 * @property Reminder[]              $reminders
 * @property ChatMemberMessageTag[]  $tags
 * @property Media[]                 $files
 * @property ChatMemberMessageView[] $views
 * @property ChatMemberMessage       $replyTo
 * @property Survey[]                $surveys
 */
class ChatMemberMessage extends AR
{
	public const DEFAULT_MEDIA_CATEGORY = 'chat_member_message';

	public const SURVEY_TEMPLATE              = 'survey';
	public const UNAVAILABLE_CONTACT_TEMPLATE = 'unavailable_contact';
	public const UNAVAILABLE_SURVEY_TEMPLATE  = 'unavailable_survey';

	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;
	protected bool $useSoftDelete = true;

	public ?bool $is_viewed = null;

	public static function tableName(): string
	{
		return 'chat_member_message';
	}

	public function rules(): array
	{
		return [
			[['to_chat_member_id'], 'required'],
			[['to_chat_member_id', 'from_chat_member_id'], 'integer'],
			[['message', 'template'], 'string'],
			[['created_at', 'updated_at'], 'safe'],
			[['from_chat_member_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatMember::className(), 'targetAttribute' => ['from_chat_member_id' => 'id']],
			[['to_chat_member_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatMember::className(), 'targetAttribute' => ['to_chat_member_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'                  => 'ID',
			'to_chat_member_id'   => 'To Chat Member ID',
			'from_chat_member_id' => 'From Chat Member ID',
			'message'             => 'Message',
			'template'            => 'Template',
			'created_at'          => 'Created At',
			'updated_at'          => 'Updated At',
		];
	}

	/**
	 * @return ActiveQuery|ChatMemberQuery
	 */
	public function getFromChatMember(): ChatMemberQuery
	{
		return $this->hasOne(ChatMember::className(), ['id' => 'from_chat_member_id']);
	}

	/**
	 * @return ActiveQuery|ChatMemberQuery
	 */
	public function getToChatMember(): ChatMemberQuery
	{
		return $this->hasOne(ChatMember::className(), ['id' => 'to_chat_member_id']);
	}

	/**
	 * @return RelationQuery|ActiveQuery
	 * @throws ErrorException
	 */
	public function getRelationFirst(): RelationQuery
	{
		return $this->morphHasMany(Relation::class, 'id', 'first');
	}

	/**
	 * @throws ErrorException
	 */
	public function getTasks(): TaskQuery
	{
		/** @var TaskQuery */
		return $this->morphHasManyVia(Task::class, 'id', 'second')
		            ->andOnCondition([Task::field('deleted_at') => null])
		            ->via('relationFirst');
	}

	/**
	 * @return ActiveQuery|AlertQuery
	 * @throws ErrorException
	 */
	public function getAlerts(): AlertQuery
	{
		return $this->morphHasManyVia(Alert::class, 'id', 'second')
		            ->via('relationFirst');
	}

	/**
	 * @return ActiveQuery|ReminderQuery
	 * @throws ErrorException
	 */
	public function getReminders(): ReminderQuery
	{
		return $this->morphHasManyVia(Reminder::class, 'id', 'second')
		            ->via('relationFirst');
	}

	/**
	 * @return ActiveQuery|UserNotificationQuery
	 * @throws ErrorException
	 */
	public function getNotifications(): UserNotificationQuery
	{
		return $this->morphHasManyVia(UserNotification::class, 'id', 'second')
		            ->via('relationFirst');
	}

	/**
	 * @return ActiveQuery
	 * @throws ErrorException
	 */
	public function getContacts(): ActiveQuery
	{
		return $this->morphHasManyVia(Contact::class, 'id', 'second')
		            ->via('relationFirst');
	}

	/**
	 * @return ActiveQuery|ChatMemberMessageTagQuery
	 * @throws ErrorException
	 */
	public function getTags(): ChatMemberMessageTagQuery
	{
		return $this->morphHasManyVia(ChatMemberMessageTag::class, 'id', 'second')
		            ->via('relationFirst');
	}

	/**
	 * @return MediaQuery
	 * @throws ErrorException
	 */
	public function getFiles(): MediaQuery
	{
		/** @var MediaQuery $query */
		$query = $this->morphHasManyVia(Media::class, 'id', 'second')
		              ->via('relationFirst');

		return $query->notDeleted();
	}

	/**
	 * @return ActiveQuery|ChatMemberMessageViewQuery
	 */
	public function getViews(): ChatMemberMessageViewQuery
	{
		return $this->hasMany(ChatMemberMessageView::class, ['chat_member_message_id' => 'id']);
	}

	/**
	 * @return ActiveQuery|ChatMemberMessageViewQuery
	 */
	public function getFromChatMemberViews(): ChatMemberMessageViewQuery
	{
		return $this->hasMany(ChatMemberMessageView::class, [
			'chat_member_message_id' => 'id',
			'chat_member_id'         => 'from_chat_member_id',
		]);
	}

	/**
	 * @throws ErrorException
	 */
	public function getSurveys(): SurveyQuery
	{
		/** @var SurveyQuery $query */
		$query = $this->morphHasManyVia(Survey::class, 'id', 'second')
		              ->andOnCondition(['!=', Survey::field('status'), Survey::STATUS_DRAFT])
		              ->via('relationFirst');

		return $query;
	}

	public function getReplyTo(): ChatMemberMessageQuery
	{
		/** @var ChatMemberMessageQuery $query */
		$query = $this->hasOne(ChatMemberMessage::class, ['id' => 'reply_to_id']);

		return $query;
	}

	public function isSystem(): bool
	{
		$modelType = $this->fromChatMember->model_type;

		if ($modelType !== User::getMorphClass()) {
			return false;
		}

		/** @var User $model */
		$model = $this->fromChatMember->model;

		return $model->isSystem();
	}


	public static function find(): ChatMemberMessageQuery
	{
		return new ChatMemberMessageQuery(get_called_class());
	}

	public static function getMorphClass(): string
	{
		return self::tableName();
	}
}
