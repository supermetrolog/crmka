<?php

declare(strict_types=1);

namespace app\resources\User;

use app\helpers\ArrayHelper;
use app\helpers\DateTimeHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\views\UserSurveyStatisticView;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use DateTimeInterface;

class UserSurveyStatisticViewResource extends JsonResource
{
	/** @var UserSurveyStatisticView[] */
	private array             $resources;
	private DateTimeInterface $startDate;
	private DateTimeInterface $endDate;

	public function __construct(array $resources, DateTimeInterface $start, DateTimeInterface $end)
	{
		$this->resources = $resources;

		$this->startDate = $start;
		$this->endDate   = $end;
	}

	public function toArray(): array
	{
		return [
			'start_date' => DateTimeHelper::tryFormat($this->startDate),
			'end_date'   => DateTimeHelper::tryFormat($this->endDate),
			'users'      => ArrayHelper::map($this->resources, static fn(UserSurveyStatisticView $resource) => [
				'user'       => UserShortResource::makeArray($resource),
				'statistics' => [
					'surveys_count'         => $resource->surveys_count,
					'calls_total_count'     => $resource->calls_total_count,
					'calls_accepted_count'  => $resource->calls_accepted_count,
					'calls_rejected_count'  => $resource->calls_rejected_count,
					'survey_tasks_count'    => $resource->survey_tasks_count,
					'completed_tasks_count' => $resource->completed_tasks_count,
				]
			])
		];
	}
}