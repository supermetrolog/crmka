<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\models\Call;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use app\models\Notification\UserNotification;
use app\models\Relation;
use app\models\Reminder;
use app\models\Task;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

/**
 * @see ChatMember
 */
class ChatMemberQuery extends AQ
{

	/**
	 * @return ChatMember[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return ChatMember|ActiveRecord|null
	 */
	public function one($db = null): ?ChatMember
	{
		return parent::one($db);
	}

	public function byModelId(int $id): self
	{
		return $this->andWhere([$this->field('model_id') => $id]);
	}

	public function byModelType(string $type): self
	{
		return $this->andWhere([$this->field('model_type') => $type]);
	}

	public function byMorph(int $id, string $type): self
	{
		return $this->byModelType($type)->byModelId($id);
	}

	public function leftJoinLastCallRelation(): self
	{
		$maxIdsSubQuery = Relation::find()
		                          ->select(['MAX(id)'])
		                          ->byFirstType(ChatMember::getMorphClass())
		                          ->bySecondType(Call::getMorphClass())
		                          ->groupBy(['first_id', 'first_type']);

		$subQuery = Relation::find()
		                    ->byFirstType(ChatMember::getMorphClass())
		                    ->bySecondType(Call::getMorphClass())
		                    ->andWhere(['id' => $maxIdsSubQuery]);

		$this->leftJoin(['last_call_rel' => $subQuery], $this->field('id') . '=' . 'last_call_rel.first_id');

		return $this;
	}

	public function joinUnreadStatistic(): self
	{
		$tasksQuery = Task::find()
		                  ->select(['COUNT(*)'])
		                  ->andWhere(['user_id' => new Expression($this->field('model_id'))])
		                  ->notCompleted()
		                  ->notDeleted();

		$remindersQuery = Reminder::find()
		                          ->select(['COUNT(*)'])
		                          ->andWhere(['user_id' => new Expression($this->field('model_id'))])
		                          ->notNotified()
		                          ->notDeleted();

		$notificationsQuery = UserNotification::find()
		                                      ->select(['COUNT(*)'])
		                                      ->andWhere([
			                                      'user_id'   => new Expression($this->field('model_id')),
			                                      'viewed_at' => null
		                                      ]);

		$messagesQuery = ChatMemberMessage::find()
		                                  ->select(['COUNT(*)'])
		                                  ->joinWith('views')
		                                  ->andWhere([ChatMemberMessage::getColumn('from_chat_member_id') => new Expression($this->field('id'))])
		                                  ->andWhere([ChatMemberMessageView::getColumn('id') => null])
		                                  ->notDeleted();

		$this->addSelect([
			'unread_task_count'         => $tasksQuery,
			'unread_reminder_count'     => $remindersQuery,
			'unread_notification_count' => $notificationsQuery,
			'unread_message_count'      => $messagesQuery,
		]);

		return $this;
	}
}
