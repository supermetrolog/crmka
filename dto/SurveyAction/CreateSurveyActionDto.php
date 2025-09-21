<?php

declare(strict_types=1);

namespace app\dto\SurveyAction;

use app\models\Survey;
use app\models\User\User;
use yii\base\BaseObject;

class CreateSurveyActionDto extends BaseObject
{
	public Survey  $survey;
	public string  $type;
	public ?int    $target_id;
	public ?string $comment;

	public ?string $completed_at = null;
	public ?string $status       = null;

	public User $createdBy;
}
