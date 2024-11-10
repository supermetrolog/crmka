<?php

namespace app\events\Survey;

use app\events\AbstractEvent;

class SurveyCompanyPlannedDevelopEvent extends AbstractEvent
{
	public int $surveyId;

	public function __construct(int $surveyId)
	{
		$this->surveyId = $surveyId;

		parent::__construct();
	}

	public function getSurveyId(): int
	{
		return $this->surveyId;
	}
}
