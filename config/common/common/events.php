<?php

use app\events\CreateSurveyEvent;
use app\listeners\CreateSurveySystemChatMessageListener;

return [
	CreateSurveyEvent::class => [
		CreateSurveySystemChatMessageListener::class
	]
];