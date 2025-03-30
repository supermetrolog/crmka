<?php

declare(strict_types=1);

namespace app\dto\Timeline;

use yii\base\BaseObject;

class TimelineStepCommentDto extends BaseObject
{
	public int     $type;
	public ?int    $letter_id = null;
	public string  $comment;
	public ?string $title     = null;
}