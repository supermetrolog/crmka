<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Forms;

use app\components\MessageTemplate\Enums\ChannelEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\Form\Form;

class ChannelForm extends Form
{
	public $channel = ChannelEnum::EMAIL;

	public function rules(): array
	{
		return [
			['channel', 'required'],
			['channel', EnumValidator::class, 'enumClass' => ChannelEnum::class]
		];
	}
}


