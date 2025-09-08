<?php

declare(strict_types=1);

namespace app\components\Notification;

use app\components\Notification\Interfaces\NotificationActionInterface;
use app\components\Notification\Interfaces\NotificationInterface;
use app\components\Notification\Interfaces\NotificationRelationInterface;
use app\components\Notification\Interfaces\NotificationTemplateInterface;

class Notification implements NotificationInterface
{
	public string $subject;
	public string $message;

	/** @var NotificationActionInterface[] */
	public array $actions;

	/** @var NotificationRelationInterface[] */
	public array $relations;

	public ?NotificationTemplateInterface $template = null;

	/**
	 * @param NotificationActionInterface[]   $actions
	 * @param NotificationRelationInterface[] $relations
	 */
	public function __construct(string $subject, string $message, ?NotificationTemplateInterface $template = null, array $actions = [], array $relations = [])
	{
		$this->subject = $subject;
		$this->message = $message;

		$this->actions   = $actions;
		$this->relations = $relations;
	}

	public function getSubject(): string
	{
		return $this->subject;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	public function getActions(): array
	{
		return $this->actions;
	}

	public function getRelations(): array
	{
		return $this->relations;
	}

	public function getTemplate(): ?NotificationTemplateInterface
	{
		return $this->template;
	}
}