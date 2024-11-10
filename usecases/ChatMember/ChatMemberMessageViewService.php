<?php

declare(strict_types=1);

namespace app\usecases\ChatMember;

use app\dto\ChatMember\CreateChatMemberMessageViewDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessageView;
use app\usecases\Notification\UserNotificationService;
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
	 * @throws Throwable
	 */
	public function create(CreateChatMemberMessageViewDto $dto): ChatMemberMessageView
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model = new ChatMemberMessageView([
				'chat_member_id'         => $dto->fromChatMember->id,
				'chat_member_message_id' => $dto->message->id,
			]);

			$model->saveOrThrow();

			foreach ($dto->message->notifications as $notification) {
				if ($notification->user_id == $dto->fromChatMember->model_id) {
					$this->notificationService->viewed($notification);
				}
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