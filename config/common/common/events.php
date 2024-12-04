<?php

use app\events\Company\ChangeConsultantCompanyEvent;
use app\events\Survey\CreateSurveyEvent;
use app\listeners\Company\ChangeConsultantCompanySystemChatMessageListener;
use app\listeners\Survey\CreateSurveySystemChatMessageListener;
use app\listeners\Survey\CreateSurveyUpdateLastCallListener;

return [
	CreateSurveyEvent::class            => [
		CreateSurveySystemChatMessageListener::class,
		CreateSurveyUpdateLastCallListener::class
	],
	ChangeConsultantCompanyEvent::class => [
		ChangeConsultantCompanySystemChatMessageListener::class
	]
];