<?php

namespace app\builders\Task;

use app\dto\Task\CreateTaskDto;
use app\helpers\ArrayHelper;
use app\helpers\DateIntervalHelper;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Task;
use app\models\User;
use app\repositories\UserRepository;
use DateTimeInterface;
use InvalidArgumentException;

class TaskBuilder
{
	protected ?int $duration            = null;
	protected bool $assignedToCreatedBy = false;
	protected int  $status              = Task::STATUS_CREATED;

	protected ?User              $user        = null;
	protected                    $createdBy   = null;
	protected ?string            $message     = null;
	protected DateTimeInterface  $start;
	protected ?DateTimeInterface $end         = null;
	protected array              $tagIds      = [];
	protected array              $observerIds = [];

	protected UserRepository $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;

		$this->setStart(DateTimeHelper::now());
	}

	public function setStatus(int $status): self
	{
		if (!ArrayHelper::includes(Task::getStatuses(), $status)) {
			throw new InvalidArgumentException('Invalid task status');
		}

		$this->status = $status;

		return $this;
	}

	protected function getStatus(): int
	{
		return $this->status;
	}

	public function setUser(User $user): self
	{
		$this->user = $user;

		return $this;
	}

	protected function getUser(): ?User
	{
		if ($this->assignedToCreatedBy) {
			return $this->createdBy;
		}

		return $this->user;
	}

	public function setMessage(string $message): self
	{
		$this->message = $message;

		return $this;
	}

	public function setStart(DateTimeInterface $dateTime): self
	{
		$this->start = $dateTime;

		return $this;
	}

	protected function getStart(): DateTimeInterface
	{
		return $this->start;
	}

	public function setEnd(DateTimeInterface $dateTime): self
	{
		$this->end = $dateTime;

		return $this;
	}

	protected function getEnd(): ?DateTimeInterface
	{
		if ($this->duration) {
			$this->end = $this->getStart()->add(DateIntervalHelper::days($this->duration));
		}

		return $this->end;
	}

	public function setDuration(int $daysDuration): self
	{
		if ($daysDuration <= 0) {
			throw new InvalidArgumentException('Duration must be greater than 0');
		}

		$this->duration = $daysDuration;

		return $this;
	}

	public function addTagId(int $tagId): self
	{
		$this->tagIds[] = $tagId;

		return $this;
	}

	public function addTagIds(array $tagIds): self
	{
		$this->tagIds = ArrayHelper::merge($this->tagIds, $tagIds);

		return $this;
	}

	public function setTagIds(array $tagIds): self
	{
		$this->tagIds = $tagIds;

		return $this;
	}

	/**
	 * @param mixed $createdBy
	 */
	public function setCreatedBy($createdBy): self
	{
		$this->createdBy = $createdBy;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCreatedBy()
	{
		return $this->createdBy;
	}

	protected function getCreatedById(): ?int
	{
		if ($this->createdBy) {
			return $this->createdBy->id;
		}

		return null;
	}

	protected function getCreatedByType(): ?string
	{
		if ($this->createdBy) {
			return $this->createdBy::getMorphClass();
		}

		return null;
	}

	public function assignToCreatedBy(): self
	{
		$this->assignedToCreatedBy = true;

		return $this;
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function assignToModerator(): self
	{
		$moderator = $this->userRepository->getModeratorOrThrow();

		$this->user = $moderator;

		return $this;
	}

	public function validateOrThrow(): void
	{
		if (is_null($this->duration) && is_null($this->end)) {
			throw new InvalidArgumentException('Duration or end date must be set');
		}

		if (is_null($this->getUser())) {
			throw new InvalidArgumentException('User must be set');
		}

		if (empty($this->message)) {
			throw new InvalidArgumentException('Message must be set');
		}

		if (is_null($this->getCreatedBy())) {
			throw new InvalidArgumentException('Created by must be set');
		}

		if ($this->assignedToCreatedBy && $this->getCreatedByType() !== User::getMorphClass()) {
			throw new InvalidArgumentException('Assigning to created by is possible only for users');
		}
	}

	public function build(): CreateTaskDto
	{
		$this->validateOrThrow();

		return new CreateTaskDto([
			'user'            => $this->getUser(),
			'message'         => $this->message,
			'status'          => $this->getStatus(),
			'start'           => $this->getStart(),
			'end'             => $this->getEnd(),
			'created_by_id'   => $this->getCreatedById(),
			'created_by_type' => $this->getCreatedByType(),
			'tagIds'          => $this->tagIds,
			'observerIds'     => $this->observerIds,
		]);
	}
}