<?php

namespace app\models\views;

use app\models\Request;

class RequestSearchView extends Request
{
	public int     $tasks_count           = 0;
	public bool    $has_pending_survey    = false;
	public ?string $pending_survey_status = null;
}
