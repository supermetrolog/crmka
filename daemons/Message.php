<?php

namespace app\daemons;

use yii\base\Model;
use yii\helpers\Json;

class Message extends Model
{
	public const ACTION_NEW_NOTIFICATION          = 'new_notifications';
	public const ACTION_NEW_USER_NOTIFICATION     = 'new_user_notification';
	public const ACTION_CHECK_NOTIFICATIONS_COUNT = 'check_notifications_count';
	public const ACTION_NEW_CALL                  = 'new_calls';
	public const ACTION_CHECK_CALLS_COUNT         = 'check_calls_count';

	private        $body;
	private string $action;
	private bool   $error = false;
	private int    $time;

	public function setBody($data): void
	{
		$this->body = $data ?? "";
	}

	public function setAction(string $data): void
	{
		$this->action = $data;
	}

	public function setError(): void
	{
		$this->error = true;
	}

	public function setTime(int $time): void
	{
		$this->time = $time;
	}

	public function getData(): string
	{
		return Json::encode([
			'message' => $this->body,
			'action'  => $this->action,
			'error'   => $this->error,
			'ts'      => $this->time ?? time(),
		]);
	}
}
