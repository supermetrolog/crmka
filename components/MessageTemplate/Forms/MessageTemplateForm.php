<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Forms;

use app\components\MessageTemplate\Dto\MessageTemplateDto;
use app\components\MessageTemplate\Interfaces\MessageTemplateDtoInterface;
use app\components\MessageTemplate\Interfaces\MessageTemplateFormInterface;
use app\kernel\common\models\Form\Form;

class MessageTemplateForm extends Form implements MessageTemplateFormInterface
{
	public function getDto(): MessageTemplateDtoInterface
	{
		return new MessageTemplateDto();
	}
}


