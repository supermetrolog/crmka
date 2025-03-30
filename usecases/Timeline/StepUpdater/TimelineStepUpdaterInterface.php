<?php

declare(strict_types=1);

namespace app\usecases\Timeline\StepUpdater;

use app\dto\Timeline\TimelineStepFeedbackDto;
use app\dto\Timeline\TimelineStepObjectDto;
use app\models\miniModels\TimelineStep;

interface TimelineStepUpdaterInterface
{
	/**
	 * @param TimelineStepObjectDto[]   $objectDtos
	 * @param TimelineStepFeedbackDto[] $feedbackDtos
	 */
	public function handle(TimelineStep $step, TimelineStep $oldStepSnapshot, array $objectDtos = [], array $feedbackDtos = []): void;

	/**
	 * @param TimelineStepObjectDto[]   $objectDtos
	 * @param TimelineStepFeedbackDto[] $feedbackDtos
	 */
	public function process(TimelineStep $step, TimelineStep $oldStepSnapshot, array $objectDtos = [], array $feedbackDtos = []): void;

	/**
	 * @param TimelineStepObjectDto[]   $objectDtos
	 * @param TimelineStepFeedbackDto[] $feedbackDtos
	 */
	public function shouldBeProcessed(TimelineStep $step, TimelineStep $oldStepSnapshot, array $objectDtos = [], array $feedbackDtos = []): bool;
}