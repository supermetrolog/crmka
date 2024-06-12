<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use app\models\Notification\UserNotification;
use app\models\Reminder;
use app\models\Task;
use yii\base\ErrorException;
use yii\db\Exception;
use yii\db\Query;

class ChatMemberRepository
{
	/**
	 * @throws ErrorException
	 * @throws Exception
	 */
	public function getUnreadStatisticByUserId(int $user_id, int $chat_member_id): array
	{
		$tasksQuery = Task::find()
		                  ->select(['COUNT(*)'])
		                  ->andWhere(['user_id' => $user_id])
		                  ->notCompleted()
		                  ->notDeleted();

		$remindersQuery = Reminder::find()
		                          ->select(['COUNT(*)'])
		                          ->andWhere(['user_id' => $user_id])
		                          ->notNotified()
		                          ->notDeleted();

		$notificationsQuery = UserNotification::find()
		                                      ->select(['COUNT(*)'])
		                                      ->andWhere([
			                                      'user_id'   => $user_id,
			                                      'viewed_at' => null
		                                      ]);

		$messagesQuery = ChatMemberMessage::find()
		                                  ->select(['COUNT(*)'])
		                                  ->joinWith('views')
		                                  ->andWhere([ChatMemberMessage::getColumn('from_chat_member_id') => $chat_member_id])
		                                  ->andWhere([ChatMemberMessageView::getColumn('id') => null])
		                                  ->notDeleted();

		return (new Query())->select([
			'tasks'         => $tasksQuery,
			'reminders'     => $remindersQuery,
			'notifications' => $notificationsQuery,
			'messages'      => $messagesQuery,
		])->createCommand()->queryOne();
	}
}
