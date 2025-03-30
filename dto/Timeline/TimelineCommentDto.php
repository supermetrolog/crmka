<?php

declare(strict_types=1);

namespace app\dto\Timeline;

use app\models\miniModels\TimelineStep;
use yii\base\BaseObject;

class TimelineCommentDto extends BaseObject
{
	public TimelineStep $timelineStep;
	public int          $type;
	public ?int         $letter_id;
	public string       $comment;
	public ?string      $title;
}