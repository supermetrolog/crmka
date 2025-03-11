<?php

declare(strict_types=1);

namespace app\dto\Request;

use yii\base\BaseObject;

class PassiveRequestDto extends BaseObject
{
	public int     $passive_why;
	public ?string $passive_why_comment = null;
}