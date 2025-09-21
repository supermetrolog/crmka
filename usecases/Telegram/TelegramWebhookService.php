<?php
declare(strict_types=1);

namespace app\usecases\Telegram;

use app\components\Telegram\Models\TUpdate;
use app\dto\Telegram\TelegramUserDataDto;
use app\enum\Telegram\TelegramUpdateCommandEnum;
use app\helpers\ArrayHelper;
use app\helpers\StringHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\repositories\UserTelegramLinkRepository;

final class TelegramWebhookService
{
	protected UserTelegramLinkRepository   $linkRepository;
	protected TransactionBeginnerInterface $transactionBeginner;
	protected UserTelegramLinkService      $userTelegramLinkService;
	protected TelegramLinkService          $linker;

	public function __construct(
		UserTelegramLinkRepository $linkRepository,
		UserTelegramLinkService $userTelegramLinkService,
		TelegramLinkService $linker,
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->linkRepository          = $linkRepository;
		$this->userTelegramLinkService = $userTelegramLinkService;
		$this->linker                  = $linker;
		$this->transactionBeginner     = $transactionBeginner;
	}

	/**
	 * @throws \Exception
	 */
	public function handleUpdate(TUpdate $update): void
	{
		$message = $update->message ?? $update->callback_query->message;

		if (!$message) {
			return;
		}

		$text     = $message->text;
		$entities = $message->entities;

		if (!$text) {
			return;
		}

		\Yii::debug($text);

		if (ArrayHelper::length($entities)) {
			\Yii::debug($entities[0]->type);
		}

		if (StringHelper::startWith($text, TelegramUpdateCommandEnum::START)) {
			// TODO: Link user
			return;
		}

		if (StringHelper::startWith($text, TelegramUpdateCommandEnum::REVOKE)) {
			$this->revokeByTelegramId($message->from->id);

			return;
		}
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws \Exception
	 */
	private function linkByCode(string $code, TUpdate $update): void
	{
		$dto = new TelegramUserDataDto([
			'telegramUserId' => $update->message->from->id,
			'firstName'      => $update->message->from->first_name,
			'lastName'       => $update->message->from->last_name,
			'username'       => $update->message->from->username,
		]);

		$this->linker->consumeTicket($code, $dto);
	}

	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 */
	private function revokeByTelegramId(int $telegramId): void
	{
		$link = $this->linkRepository->findActiveByTelegramUserIdOrThrow($telegramId);

		$this->userTelegramLinkService->revoke($link);
	}
}
