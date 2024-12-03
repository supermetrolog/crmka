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

	protected ?User              $user          = null;
	protected ?User              $createdBy     = null;
	protected ?int               $createdById   = null;
	protected ?string            $createdByType = null;
	protected ?string            $message       = null;
	protected ?DateTimeInterface $start         = null;
	protected ?DateTimeInterface $end           = null;
	protected array              $tagIds        = [];
	protected array              $observerIds   = [];

	protected UserRepository $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
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
			return $this->userRepository->findOne($this->getCreatedById());
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
		if (!is_null($this->start)) {
			return $this->start;
		}

		return DateTimeHelper::now();
	}

	public function setEnd(DateTimeInterface $dateTime): self
	{
		$this->end = $dateTime;

		return $this;
	}

	protected function getEnd(): DateTimeInterface
	{
		if ($this->duration) {
			$this->end = $this->getStart()->add(DateIntervalHelper::days($this->duration));
		}

		if (!is_null($this->end)) {
			return $this->end;
		}

		return DateTimeHelper::now();
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

	public function setCreatedBy(User $createdBy): self
	{
		$this->createdBy = $createdBy;

		return $this;
	}

	public function setCreatedById(int $createdById): self
	{
		$this->createdById = $createdById;

		return $this;
	}

	protected function getCreatedById(): ?int
	{
		if ($this->createdBy) {
			return $this->createdBy->id;
		}

		return $this->createdById;
	}

	public function setCreatedByType(string $type): self
	{
		if ($this->createdBy) {
			throw new InvalidArgumentException('Created by type already set using created by');
		}

		$this->createdByType = $type;

		return $this;
	}

	protected function getCreatedByType(): ?string
	{
		if ($this->createdBy) {
			return $this->createdBy::getMorphClass();
		}

		return $this->createdByType;
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
		$moderator = $this->userRepository->getModerator();

		if (!$moderator) {
			throw new ModelNotFoundException('Moderator not found');
		}

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

		if (is_null($this->getCreatedById())) {
			throw new InvalidArgumentException('Created by id must be set');
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