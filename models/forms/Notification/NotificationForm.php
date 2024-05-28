<?php

declare(strict_types=1);

namespace app\models\forms\Notification;

use app\dto\Notification\CreateNotificationDto;
use app\kernel\common\models\Form\Form;
use app\models\Notification\NotificationChannel;
use Exception;

class NotificationForm extends Form
{

	public const SCENARIO_CREATE = 'scenario_create';

	public $channel;
	public $subject;
	public $message;
	public $notifiable;
	public $created_by_type;
	public $created_by_id;

	public function rules(): array
	{
		return [
			[['channel', 'subject', 'message'], 'required'],
			[['channel', 'subject', 'message'], 'string'],
			['channel', 'in', 'range' => NotificationChannel::getChannels()],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'channel',
			'subject',
			'message',
		];

		return [
			self::SCENARIO_CREATE => [...$common],
		];
	}

	/**
	 * @return CreateNotificationDto
	 * @throws Exception
	 */
	public function getDto()
	{
		return new CreateNotificationDto([
			'channel'         => $this->channel,
			'subject'         => $this->subject,
			'message'         => $this->message,
			'notifiable'      => $this->notifiable,
			'created_by_type' => $this->created_by_type,
			'created_by_id'   => $this->created_by_id,
		]);
	}
}
