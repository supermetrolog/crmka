<?php

declare(strict_types=1);

namespace app\usecases\Notification;

use app\dto\UserNotification\CreateUserNotificationTemplateDto;
use app\dto\UserNotification\UpdateUserNotificationTemplateDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Notification\UserNotificationTemplate;
use Throwable;
use yii\db\StaleObjectException;

class UserNotificationTemplateService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateUserNotificationTemplateDto $dto): UserNotificationTemplate
	{
		$template = new UserNotificationTemplate();

		$template->kind      = $dto->kind;
		$template->priority  = $dto->priority;
		$template->category  = $dto->category;
		$template->is_active = $dto->isActive;

		$template->saveOrThrow();

		return $template;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(UserNotificationTemplate $template, UpdateUserNotificationTemplateDto $dto): UserNotificationTemplate
	{
		$template->priority  = $dto->priority;
		$template->category  = $dto->category;
		$template->is_active = $dto->isActive;

		$template->saveOrThrow();

		return $template;
	}

	/**
	 * @throws SaveModelException
	 */
	public function disable(UserNotificationTemplate $template): void
	{
		$template->is_active = false;

		$template->saveOrThrow();
	}

	/**
	 * @throws SaveModelException
	 */
	public function enable(UserNotificationTemplate $template): void
	{
		$template->is_active = true;

		$template->saveOrThrow();
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(UserNotificationTemplate $template): void
	{
		$template->delete();
	}
}