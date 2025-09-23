<?php

declare(strict_types=1);

namespace app\models\forms\User;

use app\dto\UserNotification\UserNotificationActionDto;
use app\kernel\common\models\Form\Form;
use Exception;
use yii\helpers\Json;

class UserNotificationActionSendForm extends Form
{
	public $label;
	public $code;
	public $type;
	public $order;
	public $icon;
	public $style;
	public $confirmation;
	public $payload;

	public function rules(): array
	{
		return [
			[['label', 'code', 'type', 'order'], 'required'],
			['label', 'string', 'max' => 64],
			['code', 'string', 'max' => 32],
			['type', 'string', 'max' => 16],
			['icon', 'string', 'max' => 64],
			['style', 'string', 'max' => 32],
			[['order'], 'integer'],
			['confirmation', 'boolean'],
			['payload', 'string'],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'label'        => 'Название',
			'code'         => 'Код',
			'type'         => 'Тип',
			'order'        => 'Порядок',
			'icon'         => 'Иконка',
			'style'        => 'Стиль',
			'confirmation' => 'Подтверждение',
			'payload'      => 'Данные'
		];
	}

	/**
	 * @throws Exception
	 */
	public function getDto(): UserNotificationActionDto
	{
		return new UserNotificationActionDto([
			'label'        => $this->label,
			'code'         => $this->code,
			'type'         => $this->type,
			'order'        => $this->order,
			'icon'         => $this->icon,
			'style'        => $this->style,
			'confirmation' => $this->confirmation,
			'payload'      => $this->payload ? Json::decode($this->payload) : null
		]);
	}
}