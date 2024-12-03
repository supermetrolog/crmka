<?php

namespace app\builders\Task;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\TaskTag;
use app\repositories\UserRepository;

class EffectTaskBuilder extends TaskBuilder
{
	protected ?int $duration = 7; // days

	/**
	 * @throws ModelNotFoundException
	 */
	public function __construct(UserRepository $userRepository)
	{
		parent::__construct($userRepository);

		$this->assignToModerator();
		$this->setTagIds([TaskTag::SURVEY_TASK_TAG_ID]);
	}
}