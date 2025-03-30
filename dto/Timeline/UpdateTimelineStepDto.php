<?php

declare(strict_types=1);

namespace app\dto\Timeline;

use DateTimeInterface;
use yii\base\BaseObject;

class UpdateTimelineStepDto extends BaseObject
{
	public ?string            $comment;
	public ?int               $done;
	public ?int               $negative;
	public ?int               $additional;
	public ?DateTimeInterface $date;
	public ?int               $status;

	/** @var int[] */
	public array $feedback_ways = [];
}