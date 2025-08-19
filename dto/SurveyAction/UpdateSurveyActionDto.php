<?php

declare(strict_types=1);

namespace app\dto\SurveyAction;

use yii\base\BaseObject;

class UpdateSurveyActionDto extends BaseObject
{
	public ?int    $target_id;
	public ?string $comment;

	public ?string $completed_at = null;
	public ?string $status       = null;
}
