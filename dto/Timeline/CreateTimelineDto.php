<?php

declare(strict_types=1);

namespace app\dto\Timeline;

use yii\base\BaseObject;

class CreateTimelineDto extends BaseObject
{
	public int $request_id;
	public int $consultant_id;
}