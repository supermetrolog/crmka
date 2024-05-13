<?php

declare(strict_types=1);

namespace app\usecases\ChatMember;

use app\dto\Alert\CreateAlertDto;
use app\dto\ChatMember\CreateChatMemberMessageDto;
use app\dto\ChatMember\UpdateChatMemberMessageDto;
use app\dto\Relation\CreateRelationDto;
use app\dto\Task\CreateTaskDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Alert;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageTag;
use app\models\Contact;
use app\models\Task;
use app\usecases\Alert\CreateAlertService;
use app\usecases\Relation\RelationService;
use app\usecases\Task\CreateTaskService;
use Throwable;
use yii\db\Exception;

class ChatMemberMessageService
{
	private TransactionBeginnerInterface $transactionBeginner;
	protected CreateTaskService          $createTaskService;
	protected CreateAlertService         $createAlertService;
	protected RelationService            $relationService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		CreateTaskService $createTaskService,
		RelationService $relationService,
		CreateAlertService $createAlertService
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->createTaskService   = $createTaskService;
		$this->relationService     = $relationService;
		$this->createAlertService  = $createAlertService;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CreateChatMemberMessageDto $dto): ChatMemberMessage
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$message = new ChatMemberMessage();

			$message->from_chat_member_id = $dto->from->id;
			$message->to_chat_member_id   = $dto->to->id;

			$message->message = $dto->message;

			$message->saveOrThrow();

			foreach ($dto->contactIds as $contactId) {
				$this->relationService->create(new CreateRelationDto([
					'first_type'  => $message::getMorphClass(),
					'first_id'    => $message->id,
					'second_type' => Contact::getMorphClass(),
					'second_id'   => $contactId,
				]));
			}

			foreach ($dto->tagIds as $tagId) {
				$this->relationService->create(new CreateRelationDto([
					'first_type'  => $message::getMorphClass(),
					'first_id'    => $message->id,
					'second_type' => ChatMemberMessageTag::getMorphClass(),
					'second_id'   => $tagId,
				]));
			}

			$message->refresh();

			$tx->commit();

			return $message;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
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
		$tx = $this->transactionBeginner->begin();

		try {
			$message = $this->create($createChatMemberMessageDto);
			$this->createTask($message, $createTaskDto);

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
	public function createTask(ChatMemberMessage $message, CreateTaskDto $createTaskDto): Task
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$task = $this->createTaskService->create($createTaskDto);

			$this->relationService->create(new CreateRelationDto([
				'first_type'  => $message::getMorphClass(),
				'first_id'    => $message->id,
				'second_type' => $task::getMorphClass(),
				'second_id'   => $task->id,
			]));

			$tx->commit();

			return $task;
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
	public function createAlert(ChatMemberMessage $message, CreateAlertDto $createAlertDto): Alert
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$alert = $this->createAlertService->create($createAlertDto);

			$this->relationService->create(new CreateRelationDto([
				'first_type'  => $message::getMorphClass(),
				'first_id'    => $message->id,
				'second_type' => $alert::getMorphClass(),
				'second_id'   => $alert->id,
			]));

			$tx->commit();

			return $alert;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}
}