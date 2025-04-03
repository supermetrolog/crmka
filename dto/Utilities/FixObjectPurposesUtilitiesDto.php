<?php

declare(strict_types=1);

namespace app\dto\Utilities;

use app\models\Objects;
use yii\base\BaseObject;

class FixObjectPurposesUtilitiesDto extends BaseObject
{
	public Objects $object;
	public array   $purposes;
}