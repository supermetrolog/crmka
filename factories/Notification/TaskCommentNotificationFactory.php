<?php

declare(strict_types=1);

namespace app\factories\Notification;

use app\components\Formatter;
use app\components\Notification\Builders\NotificationActionBuilder;
use app\components\Notification\Notification;
use app\components\Notification\NotificationAction;
use app\components\Notification\NotificationRelation;
use app\enum\Notification\UserNotificationTemplateKindEnum;
use app\models\TaskComment;
use app\repositories\UserNotificationTemplateRepository;

class TaskCommentNotificationFactory
{
	protected UserNotificationTemplateRepository $templateRepository;
	protected Formatter                          $formatter;

	public function __construct(UserNotificationTemplateRepository $templateRepository, Formatter $formatter)
	{
		$this->templateRepository = $templateRepository;
		$this->formatter          = $formatter;
	}

	protected function makeOpenTaskAction(TaskComment $taskComment, int $order = 0): NotificationAction
	{
		return NotificationActionBuilder::command()
		                                ->label('Открыть задачу')
		                                ->code('open_task_comment')
		                                ->icon('task')
		                                ->order($order)
		                                ->payload([
			                                'task_id'         => $taskComment->task_id,
			                                'task_comment_id' => $taskComment->id,
			                                'focus'           => 'comments'
		                                ])
		                                ->build();
	}

	protected function makeReplyCommentAction(TaskComment $taskComment, int $order = 0): NotificationAction
	{
		return NotificationActionBuilder::command()
		                                ->label('Ответить')
		                                ->code('reply_task_comment')
		                                ->icon('reply')
		                                ->order($order)
		                                ->payload([
			                                'task_id'         => $taskComment->task_id,
			                                'task_comment_id' => $taskComment->id,
			                                'focus'           => 'comments'
		                                ])
		                                ->build();
	}

	public function created(TaskComment $comment): Notification
	{
		$subject = 'Новый комментарий к задаче';

		$message = sprintf(
			'%s %s комментарий к задаче "%s" (%d)',
			$comment->createdBy->userProfile->getMediumName(),
			$this->formatter->genderize($comment->createdBy->userProfile->gender, 'оставил', 'оставила'),
			$comment->task->title,
			$comment->task->id
		);

		$template = $this->templateRepository->findOneByKind(UserNotificationTemplateKindEnum::CREATE_TASK_COMMENT);

		$actions = [
			$this->makeOpenTaskAction($comment, 1),
			$this->makeReplyCommentAction($comment, 2)
		];

		$relations = [
			NotificationRelation::from($comment),
			NotificationRelation::from($comment->task),
		];

		return new Notification($subject, $message, $template, $actions, $relations);
	}
}