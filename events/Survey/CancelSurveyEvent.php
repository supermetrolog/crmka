<?php

namespace app\events\Survey;

use app\events\AbstractEvent;
use app\models\ChatMember;
use app\models\Survey;

class CancelSurveyEvent extends AbstractEvent
{
	public Survey $survey;

	public function __construct(Survey $survey)
	{
		$this->survey = $survey;

		parent::__construct();
	}

	public function getSurvey(): Survey
	{
		return $this->survey;
	}

	public function getChatMember(): ChatMember
	{
		return $this->getSurvey()->chatMember;
	}
}
