<?php

declare(strict_types=1);

namespace app\models\forms\Notification;

use app\dto\Notification\CreateNotificationDto;
use app\kernel\common\models\Form\Form;
use app\models\Notification\NotificationChannel;
use app\models\User\User;
use Exception;

class NotificationForm extends Form
{

	public const SCENARIO_CREATE = 'scenario_create';

	public $channel;
	public $subject;
	public $message;
	public $user_id;

	public function rules(): array
	{
		return [
			[['channel', 'subject', 'message', 'user_id'], 'required'],
			[['channel', 'subject', 'message'], 'string'],
			[['user_id'], 'integer'],
			['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			['channel', 'in', 'range' => NotificationChannel::getChannels()],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'channel',
			'subject',
			'message',
			'user_id',
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
		$user = User::find()->byId($this->user_id)->one();

		return new CreateNotificationDto([
			'channel'         => $this->channel,
			'subject'         => $this->subject,
			'message'         => $this->message,
			'notifiable'      => $user,
			'created_by_type' => $user->getMorphClass(),
			'created_by_id'   => $user->id,
		]);
	}
}
