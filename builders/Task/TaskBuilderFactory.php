<?php

namespace app\builders\Task;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\repositories\UserRepository;

class TaskBuilderFactory
{
	private UserRepository $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	public function createDefaultBuilder(): TaskBuilder
	{
		return new TaskBuilder($this->userRepository);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function createEffectBuilder(): EffectTaskBuilder
	{
		return new EffectTaskBuilder($this->userRepository);
	}
}