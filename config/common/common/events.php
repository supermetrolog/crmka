<?php

use app\events\Company\ChangeConsultantCompanyEvent;
use app\events\Survey\CreateSurveyEvent;
use app\listeners\Company\ChangeConsultantCompanySystemChatMessageListener;
use app\listeners\Survey\CreateSurveySystemChatMessageListener;
use app\listeners\Survey\CreateSurveyUpdateLastCallListener;
use app\listeners\Survey\QuestionAnswerEffectListener;

return [
	CreateSurveyEvent::class            => [
		CreateSurveySystemChatMessageListener::class,
		CreateSurveyUpdateLastCallListener::class,
		QuestionAnswerEffectListener::class
	],
	ChangeConsultantCompanyEvent::class => [
		ChangeConsultantCompanySystemChatMessageListener::class
	]
];