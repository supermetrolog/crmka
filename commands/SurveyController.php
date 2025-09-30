<?php

declare(strict_types=1);

namespace app\commands;

use app\helpers\DateIntervalHelper;
use app\helpers\DateTimeHelper;
use app\kernel\common\controller\ConsoleController;
use app\models\Survey;
use app\usecases\Survey\SurveyService;
use Throwable;
use yii\console\ExitCode;
use yii\db\StaleObjectException;

class SurveyController extends ConsoleController
{
	protected SurveyService $service;

	public function __construct($id, $module, SurveyService $service, $config = [])
	{
		$this->service = $service;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function actionCleanupExpiredDrafts(int $days = 30): int
	{
		$date = DateTimeHelper::now()->sub(DateIntervalHelper::days($days));

		$this->commentf(
			'[%s] Cleaning up expired drafts older than %s',
			DateTimeHelper::nowf(),
			DateTimeHelper::format($date, 'Y-m-d')
		);

		$query = Survey::find()
		               ->notDeleted()
		               ->byStatus(Survey::STATUS_DRAFT)
		               ->andWhere(['<', 'updated_at', DateTimeHelper::format($date)])
		               ->with('user.userProfile');

		$count = 0;

		/** @var Survey $survey */
		foreach ($query->each() as $survey) {
			$this->service->delete($survey);

			$this->infof('Deleted draft #%d (author: %s)', $survey->id, $survey->user->userProfile->mediumName);

			$count++;
		}

		if ($count > 0) {
			$this->commentf('Deleted %d expired drafts', $count);
		} else {
			$this->comment('No expired drafts found');
		}

		return ExitCode::OK;
	}
}