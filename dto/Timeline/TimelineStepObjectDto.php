<?php

declare(strict_types=1);

namespace app\dto\Timeline;

use app\models\OfferMix;
use yii\base\BaseObject;

class TimelineStepObjectDto extends BaseObject
{
	public int     $offer_id;
	public int     $object_id;
	public int     $type_id;
	public ?int    $status;
	public ?int    $option;
	public ?string $comment;

	public OfferMix $offerMix;
}