<?php

declare(strict_types=1);

namespace app\components\Notification;

use app\components\Notification\Interfaces\NotificationInterface;

class Notification implements NotificationInterface
{
	public string $subject;
	public string $message;

	public function __construct(string $subject, string $message)
	{
		$this->subject = $subject;
		$this->message = $message;
	}

	public function getSubject(): string
	{
		return $this->subject;
	}

	public function getMessage(): string
	{
		return $this->message;
	}
}