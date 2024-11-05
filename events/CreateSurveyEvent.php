<?php

namespace app\events;

use app\models\ChatMember;
use app\models\Survey;
use yii\base\Event;

class CreateSurveyEvent extends Event
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
