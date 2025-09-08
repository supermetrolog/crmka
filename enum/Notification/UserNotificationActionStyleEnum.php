<?php

namespace app\enum\Notification;

use app\enum\AbstractEnum;

class UserNotificationActionStyleEnum extends AbstractEnum
{
	public const SUCCESS = 'success';
	public const DANGER  = 'danger';
	public const PRIMARY = 'primary';
	public const LIGHT   = 'light';
}