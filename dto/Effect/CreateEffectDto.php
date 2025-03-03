<?php

declare(strict_types=1);

namespace app\dto\Effect;

use yii\base\BaseObject;

class CreateEffectDto extends BaseObject
{
	public string  $title;
	public string  $kind;
	public ?string $description;
	public bool    $active = true;
}
