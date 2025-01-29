<?php

use app\events\Company\ChangeConsultantCompanyEvent;
use app\events\Contact\CreateContactEvent;
use app\events\Contact\UpdateContactEvent;
use app\events\Survey\CreateSurveyEvent;
use app\events\Task\AssignTaskEvent;
use app\events\Task\ChangeStatusTaskEvent;
use app\events\Task\CreateTaskEvent;
use app\events\Task\DeleteTaskEvent;
use app\events\Task\ObserveTaskEvent;
use app\events\Task\RestoreTaskEvent;
use app\events\Task\UpdateTaskEvent;
use app\listeners\Company\ChangeCompanyStatusListener;
use app\listeners\Company\ChangeConsultantCompanySystemChatMessageListener;
use app\listeners\Survey\CreateSurveySystemChatMessageListener;
use app\listeners\Survey\CreateSurveyUpdateLastCallListener;
use app\listeners\Task\AssignTaskListener;
use app\listeners\Task\ChangeStatusTaskListener;
use app\listeners\Task\CreateHistoryTaskListener;
use app\listeners\Task\CreateTaskListener;
use app\listeners\Task\DeleteTaskListener;
use app\listeners\Task\ObserveTaskListener;
use app\listeners\Task\RestoreTaskListener;
use app\listeners\Task\UpdateTaskListener;

return [
	CreateSurveyEvent::class            => [
		CreateSurveySystemChatMessageListener::class,
		CreateSurveyUpdateLastCallListener::class
	],
	ChangeConsultantCompanyEvent::class => [
		ChangeConsultantCompanySystemChatMessageListener::class
	],
	CreateTaskEvent::class              => [
		CreateHistoryTaskListener::class,
		CreateTaskListener::class
	],
	AssignTaskEvent::class              => [
		CreateHistoryTaskListener::class,
		AssignTaskListener::class
	],
	DeleteTaskEvent::class              => [
		CreateHistoryTaskListener::class,
		DeleteTaskListener::class
	],
	RestoreTaskEvent::class             => [
		CreateHistoryTaskListener::class,
		RestoreTaskListener::class
	],
	ChangeStatusTaskEvent::class        => [
		CreateHistoryTaskListener::class,
		ChangeStatusTaskListener::class
	],
	UpdateTaskEvent::class              => [
		CreateHistoryTaskListener::class,
		UpdateTaskListener::class
	],
	ObserveTaskEvent::class             => [
		CreateHistoryTaskListener::class,
		ObserveTaskListener::class
	],
	CreateContactEvent::class           => [
		ChangeCompanyStatusListener::class
	],
	UpdateContactEvent::class           => [
		ChangeCompanyStatusListener::class
	]
];