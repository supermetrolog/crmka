<?php

declare(strict_types=1);

namespace app\usecases\ChatMember;

use app\dto\ChatMember\CreateChatMemberMessageDto;
use app\dto\ChatMember\UpdateChatMemberMessageDto;
use app\dto\task\CreateTaskDto;
use app\exceptions\domain\model\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageTask;
use app\usecases\TaskService;
use Throwable;
use yii\db\Connection;
use yii\db\Exception;

class ChatMemberMessageService
{
	private Connection    $db;
	protected TaskService $taskService;

	public function __construct(
		Connection $db,
		TaskService $taskService
	)
	{
		$this->db          = $db;
		$this->taskService = $taskService;
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateChatMemberMessageDto $dto): ChatMemberMessage
	{
		$message = new ChatMemberMessage();

		$message->from_chat_member_id = $dto->from->id;
		$message->to_chat_member_id   = $dto->to->id;

		$message->message = $dto->message;

		$message->saveOrThrow();

		return $message;
	}


	/**
	 * @throws SaveModelException
	 */
	public function update(ChatMemberMessage $message, UpdateChatMemberMessageDto $dto): ChatMemberMessage
	{
		$message->message = $dto->message;

		$message->saveOrThrow();

		return $message;
	}

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function createWithTask(CreateChatMemberMessageDto $createChatMemberMessageDto, CreateTaskDto $createTaskDto): ChatMemberMessage
	{
		$tx = $this->db->beginTransaction();

		try {
			$message = $this->create($createChatMemberMessageDto);
			$this->taskService->create($createTaskDto);

			$tx->commit();

			return $message;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function createTask(ChatMemberMessage $message, CreateTaskDto $createTaskDto): ChatMemberMessageTask
	{
		$tx = $this->db->beginTransaction();

		try {
			$task = $this->taskService->create($createTaskDto);

			$messageTask                         = new ChatMemberMessageTask();
			$messageTask->task_id                = $task->id;
			$messageTask->chat_member_message_id = $message->id;

			$messageTask->saveOrThrow();

			$tx->commit();

			return $messageTask;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}
}