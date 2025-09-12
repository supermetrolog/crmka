<?php

declare(strict_types=1);

namespace app\models\forms\Notification;

use app\dto\UserNotification\CreateUserNotificationTemplateDto;
use app\dto\UserNotification\UpdateUserNotificationTemplateDto;
use app\kernel\common\models\Form\Form;

class UserNotificationTemplateForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $kind;
	public $priority;
	public $category;
	public $is_active;

	public function rules(): array
	{
		return [
			[['kind', 'priority', 'category', 'is_active'], 'required'],
			['kind', 'string', 'max' => 32],
			['priority', 'string', 'max' => 16],
			['category', 'string', 'max' => 32],
			[['is_active'], 'boolean'],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'kind'      => 'Kind',
			'priority'  => 'Приоритет',
			'category'  => 'Категория',
			'is_active' => 'Активность'
		];
	}

	public function scenarios(): array
	{
		$common = [
			'priority',
			'category',
			'is_active'
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'kind'],
			self::SCENARIO_UPDATE => $common,
		];
	}

	/**
	 * @return CreateUserNotificationTemplateDto|UpdateUserNotificationTemplateDto
	 */
	public function getDto()
	{
		if ($this->getScenario() === self::SCENARIO_CREATE) {
			return new CreateUserNotificationTemplateDto([
				'kind'     => $this->kind,
				'priority' => $this->priority,
				'category' => $this->category,
				'isActive' => $this->is_active
			]);
		}

		return new UpdateUserNotificationTemplateDto([
			'priority' => $this->priority,
			'category' => $this->category,
			'isActive' => $this->is_active
		]);
	}
}