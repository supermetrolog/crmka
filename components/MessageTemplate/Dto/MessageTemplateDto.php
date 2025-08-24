<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Dto;

use app\components\MessageTemplate\Interfaces\MessageTemplateDtoInterface;
use app\models\User;
use yii\base\BaseObject;

class MessageTemplateDto extends BaseObject implements MessageTemplateDtoInterface
{
	public User $user;
}