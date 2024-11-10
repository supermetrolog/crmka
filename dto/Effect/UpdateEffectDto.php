<?php

declare(strict_types=1);

namespace app\dto\Effect;

use yii\base\BaseObject;

class UpdateEffectDto extends BaseObject
{
	public string  $title;
	public ?string $description;
}
