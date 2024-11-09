<?php

use app\events\Company\ChangeConsultantCompanyEvent;
use app\events\Survey\CreateSurveyEvent;
use app\events\Survey\SurveyCompanyPlannedDevelopEvent;
use app\events\Survey\SurveyRequestsNoLongerRelevantEvent;
use app\listeners\Company\ChangeConsultantCompanySystemChatMessageListener;
use app\listeners\Survey\CreateSurveySystemChatMessageListener;
use app\listeners\Survey\CreateSurveyUpdateLastCallListener;
use app\listeners\Survey\SurveyCompanyPlannedDevelopListener;
use app\listeners\Survey\SurveyRequestsNoLongerRelevantListener;

return [
	CreateSurveyEvent::class                   => [
		CreateSurveySystemChatMessageListener::class,
		CreateSurveyUpdateLastCallListener::class
	],
	ChangeConsultantCompanyEvent::class        => [
		ChangeConsultantCompanySystemChatMessageListener::class
	],
	SurveyRequestsNoLongerRelevantEvent::class => [
		SurveyRequestsNoLongerRelevantListener::class
	],
	SurveyCompanyPlannedDevelopEvent::class    => [
		SurveyCompanyPlannedDevelopListener::class
	]
];