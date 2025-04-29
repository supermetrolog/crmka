<?php

use app\events\Company\ChangeConsultantCompanyEvent;
use app\events\Company\DisableCompanyEvent;
use app\events\Request\RequestActivatedEvent;
use app\events\Request\RequestDeactivatedEvent;
use app\events\Survey\CreateSurveyEvent;
use app\events\Survey\UpdateSurveyEvent;
use app\events\Task\AssignTaskEvent;
use app\events\Task\ChangeStatusTaskEvent;
use app\events\Task\CreateFileTaskEvent;
use app\events\Task\CreateTaskEvent;
use app\events\Task\DeleteFileTaskEvent;
use app\events\Task\DeleteTaskEvent;
use app\events\Task\ObserveTaskEvent;
use app\events\Task\PostponeTaskEvent;
use app\events\Task\RestoreTaskEvent;
use app\events\Task\UpdateTaskEvent;
use app\events\Timeline\UpdateTimelineStepEvent;
use app\listeners\Company\ChangeConsultantCompanySystemChatMessageListener;
use app\listeners\Company\DeactivateCompanyRequestsListener;
use app\listeners\Company\DisableCompanySystemChatMemberMessageListener;
use app\listeners\Survey\CreateSurveySystemChatMessageListener;
use app\listeners\Survey\UpdateSurveySystemChatMessageListener;
use app\listeners\Task\AssignTaskListener;
use app\listeners\Task\ChangeStatusTaskListener;
use app\listeners\Task\CreateFileTaskListener;
use app\listeners\Task\CreateHistoryTaskListener;
use app\listeners\Task\CreateTaskListener;
use app\listeners\Task\DeleteFileTaskListener;
use app\listeners\Task\DeleteTaskListener;
use app\listeners\Task\ObserveTaskListener;
use app\listeners\Task\PostponeTaskListener;
use app\listeners\Task\RestoreTaskListener;
use app\listeners\Task\UpdateTaskListener;
use app\listeners\Timeline\SyncTimelineOnRequestActivationListener;
use app\listeners\Timeline\SyncTimelineOnRequestDeactivationListener;
use app\listeners\Timeline\UpdateRequestRelationTimestampListener;

return [
	CreateSurveyEvent::class            => [
		CreateSurveySystemChatMessageListener::class
	],
	UpdateSurveyEvent::class            => [
		UpdateSurveySystemChatMessageListener::class,
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
	CreateFileTaskEvent::class          => [
		CreateHistoryTaskListener::class,
		CreateFileTaskListener::class
	],
	DeleteFileTaskEvent::class          => [
		CreateHistoryTaskListener::class,
		DeleteFileTaskListener::class
	],
	PostponeTaskEvent::class            => [
		CreateHistoryTaskListener::class,
		PostponeTaskListener::class
	],
	UpdateTimelineStepEvent::class      => [
		UpdateRequestRelationTimestampListener::class
	],
	RequestActivatedEvent::class        => [
		SyncTimelineOnRequestActivationListener::class
	],
	RequestDeactivatedEvent::class      => [
		SyncTimelineOnRequestDeactivationListener::class
	],
	DisableCompanyEvent::class          => [
		DeactivateCompanyRequestsListener::class,
		DisableCompanySystemChatMemberMessageListener::class
	]
];