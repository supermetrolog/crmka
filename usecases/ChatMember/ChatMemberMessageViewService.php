<?php

declare(strict_types=1);

namespace app\usecases\ChatMember;

use app\components\Notification\Factories\NotifierFactory;
use app\dto\ChatMember\CreateChatMemberMessageViewDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageView;
use app\usecases\Notification\UserNotificationService;
use app\usecases\Reminder\ReminderService;
use mysql_xdevapi\DocResult;
use Throwable;

class ChatMemberMessageViewService
{
	private TransactionBeginnerInterface $transactionBeginner;
	protected UserNotificationService    $notificationService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		UserNotificationService $notificationService
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->notificationService = $notificationService;
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateChatMemberMessageViewDto $dto): ChatMemberMessageView
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model = new ChatMemberMessageView([
				'chat_member_id'         => $dto->message->from_chat_member_id,
				'chat_member_message_id' => $dto->message->id,
			]);

			$model->saveOrThrow();

			foreach ($dto->message->notifications as $notification) {
				$this->notificationService->viewed($notification);
			}

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws Throwable
	 */
	public function delete(ChatMemberMessageView $model): void
	{
		$model->delete();
	}
}