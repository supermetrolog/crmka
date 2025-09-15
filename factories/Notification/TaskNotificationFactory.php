<?php

declare(strict_types=1);

namespace app\factories\Notification;

use app\components\Formatter;
use app\components\Notification\Builders\NotificationActionBuilder;
use app\components\Notification\Notification;
use app\components\Notification\NotificationAction;
use app\components\Notification\NotificationRelation;
use app\enum\Notification\UserNotificationTemplateKindEnum;
use app\models\Task;
use app\models\User;
use app\repositories\UserNotificationTemplateRepository;

class TaskNotificationFactory
{
	protected UserNotificationTemplateRepository $templateRepository;
	protected Formatter                          $formatter;

	public function __construct(UserNotificationTemplateRepository $templateRepository, Formatter $formatter)
	{
		$this->templateRepository = $templateRepository;
		$this->formatter          = $formatter;
	}

	protected function makeOpenTaskAction(Task $task, int $order = 0): NotificationAction
	{
		return NotificationActionBuilder::command()
		                                ->label('Открыть задачу')
		                                ->code('open_task')
		                                ->icon('task')
		                                ->order($order)
		                                ->payload([
			                                'task_id' => $task->id
		                                ])
		                                ->build();
	}

	public function assigned(Task $task, User $initiator): Notification
	{
		$subject = 'Назначение исполнителем задачи';

		$message = sprintf(
			'%s %s вас исполнителем задачи "%s" (%d)',
			$initiator->userProfile->getMediumName(),
			$this->formatter->genderize($initiator->userProfile->gender, 'назначил', 'назначила'),
			$task->title,
			$task->id
		);

		$template = $this->templateRepository->findOneByKind(UserNotificationTemplateKindEnum::ASSIGN_TASK);

		$actions = [
			$this->makeOpenTaskAction($task)
		];

		$relations = [
			NotificationRelation::from($task),
		];

		return new Notification($subject, $message, $template, $actions, $relations);
	}
}