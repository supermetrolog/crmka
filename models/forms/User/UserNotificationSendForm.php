<?php

declare(strict_types=1);

namespace app\models\forms\User;

use app\dto\UserNotification\SendUserNotificationDto;
use app\kernel\common\models\Form\Form;
use app\models\Notification\UserNotificationTemplate;
use app\models\User\User;
use Exception;

class UserNotificationSendForm extends Form
{
	public $subject;
	public $message;
	public $user_id;
	public $template_id;
	public $expires_at;
	public $channel;

	public function rules(): array
	{
		return [
			[['subject', 'message', 'user_id', 'channel'], 'required'],
			['subject', 'string', 'max' => 255],
			['message', 'string'],
			['channel', 'string'],
			[['user_id', 'template_id'], 'integer'],
			['expires_at', 'safe'],
			['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
			['template_id', 'exist', 'targetClass' => UserNotificationTemplate::class, 'targetAttribute' => 'id']
		];
	}

	public function attributeLabels(): array
	{
		return [
			'subject'     => 'Заголовок',
			'message'     => 'Сообщение',
			'user_id'     => 'ID сотрудника',
			'template_id' => 'Шаблон',
			'expires_at'  => 'Дата истечения',
			'channel'     => 'Канал уведомления'
		];
	}

	/**
	 * @throws Exception
	 */
	public function getDto(): SendUserNotificationDto
	{
		return new SendUserNotificationDto([
			'userId'     => $this->user_id,
			'subject'    => $this->subject,
			'message'    => $this->message,
			'templateId' => $this->template_id,
			'expiresAt'  => $this->expires_at,
			'channel'    => $this->channel
		]);
	}
}