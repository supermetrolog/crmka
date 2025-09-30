<?php

namespace app\models\views;

use app\models\User\User;

class UserSurveyStatisticView extends User
{
	public ?int $surveys_count         = null;
	public ?int $calls_total_count     = null;
	public ?int $calls_accepted_count  = null;
	public ?int $calls_rejected_count  = null;
	public ?int $survey_tasks_count    = null;
	public ?int $completed_tasks_count = null;
}
