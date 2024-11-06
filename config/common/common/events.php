<?php

use app\events\Company\ChangeConsultantCompanyEvent;
use app\events\CreateSurveyEvent;
use app\listeners\ChangeConsultantCompanySystemChatMessageListener;
use app\listeners\CreateSurveySystemChatMessageListener;

return [
	CreateSurveyEvent::class            => [
		CreateSurveySystemChatMessageListener::class
	],
	ChangeConsultantCompanyEvent::class => [
		ChangeConsultantCompanySystemChatMessageListener::class
	]
];