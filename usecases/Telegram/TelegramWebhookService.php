<?php
declare(strict_types=1);

namespace app\usecases\Telegram;

use app\components\Notification\RabbitMqWebsocketPublisher;
use app\components\Telegram\Models\TMessage;
use app\components\Telegram\Models\TUpdate;
use app\components\Telegram\TelegramBotApiClient;
use app\components\Telegram\TelegramInlineKeyboardBuilder;
use app\components\Telegram\TelegramMessageAnswerBuilder;
use app\daemons\Message;
use app\dto\Telegram\TelegramUserDataDto;
use app\enum\Telegram\TelegramMessageEntityTypeEnum;
use app\enum\Telegram\TelegramUpdateCommandEnum;
use app\exceptions\services\Telegram\UserTelegramTicketIsConsumedException;
use app\exceptions\services\Telegram\UserTelegramTicketIsExpiredException;
use app\helpers\StringHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\User\UserTelegramLink;
use app\repositories\UserTelegramLinkRepository;
use app\services\Link\CrmLinkGenerator;
use Throwable;
use Yii;
use yii\httpclient\Exception;

final class TelegramWebhookService
{
	protected UserTelegramLinkRepository   $linkRepository;
	protected TransactionBeginnerInterface $transactionBeginner;
	protected UserTelegramLinkService      $userTelegramLinkService;
	protected TelegramLinkService          $linker;
	protected TelegramBotApiClient         $bot;
	protected CrmLinkGenerator             $linkGenerator;
	protected RabbitMqWebsocketPublisher   $publisher;

	public function __construct(
		UserTelegramLinkRepository $linkRepository,
		UserTelegramLinkService $userTelegramLinkService,
		TelegramLinkService $linker,
		TransactionBeginnerInterface $transactionBeginner,
		TelegramBotApiClient $bot,
		CrmLinkGenerator $linkGenerator,
		RabbitMqWebsocketPublisher $publisher
	)
	{
		$this->linkRepository          = $linkRepository;
		$this->userTelegramLinkService = $userTelegramLinkService;
		$this->linker                  = $linker;
		$this->transactionBeginner     = $transactionBeginner;
		$this->bot                     = $bot;
		$this->linkGenerator           = $linkGenerator;
		$this->publisher               = $publisher;
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

		if (!$message->hasEntityType(TelegramMessageEntityTypeEnum::BOT_COMMAND)) {
			return;
		}

		$text = $message->text;

		if (StringHelper::startWith($text, TelegramUpdateCommandEnum::START)) {
			$this->handleStart($message);

			return;
		}

		if (StringHelper::startWith($text, TelegramUpdateCommandEnum::REVOKE)) {
			$this->handleRevoke($message);

			return;
		}

		if (StringHelper::startWith($text, TelegramUpdateCommandEnum::STATUS)) {
			$this->handleStatus($message);
		}
	}

	/**
	 * @throws Exception
	 */
	private function handleStart(TMessage $message): void
	{
		$code = StringHelper::substr($message->text, StringHelper::length(TelegramUpdateCommandEnum::START) + 1);

		if (StringHelper::length($code) < 16) {
			$this->handleStatus($message);

			return;
		}

		try {
			$link = $this->linkByCode($code, $message);

			$builder = TelegramMessageAnswerBuilder::create()
			                                       ->setText(sprintf('✅ Ваш Telegram аккаунт связан с профилем **%s**', $link->user->userProfile->mediumName))
			                                       ->addInlineKeyboardButton(TelegramInlineKeyboardBuilder::link('↗️ Перейти в CRM', $this->linkGenerator->generate('account.integrations'))->toArray());

			$this->sendAnswer($message, $builder->toArray());

			$this->publisher->publishToUser($link->user_id, ['link_id' => $link->id], Message::TELEGRAM_LINKED);
		} catch (ModelNotFoundException $th) {
			$this->sendTextAnswer($message, '❌ Неправильный код профиля CRM.');
		} catch (UserTelegramTicketIsExpiredException $th) {
			$this->sendTextAnswer($message, '⌛️ Код профиля CRM устарел. Обновите код в личном кабинете.');
		} catch (UserTelegramTicketIsConsumedException $th) {
			$this->sendTextAnswer($message, '🔄 Код профиля уже использован. Обновите код в личном кабинете.');
		} catch (Throwable $th) {
			$this->sendTextAnswer($message, '🔴 Произошла ошибка. Обновите код в личном кабинете или свяжитесь с программистом.');
			Yii::error($th->getMessage());
		}
	}

	/**
	 * @throws Exception
	 */
	private function handleRevoke(TMessage $message): void
	{
		try {
			$revokedLink = $this->revokeByTelegramId($message->from->id);

			$this->sendTextAnswer($message, sprintf('✅ Профиль **%s** отвязан от вашего Telegram аккаунта', $revokedLink->user->userProfile->mediumName));
		} catch (ModelNotFoundException $th) {
			$this->sendTextAnswer($message, '❕ К вашему аккаунту не привязан профиль в CRM.');
		} catch (Throwable $th) {
			$this->sendTextAnswer($message, '🔴 Произошла ошибка. Попробуйте позже или свяжитесь с программистом.');
			Yii::error($th->getMessage());
		}
	}

	/**
	 * @throws Exception
	 */
	private function handleStatus(TMessage $message): void
	{
		$link = $this->linkRepository->findActiveByTelegramUserId($message->from->id);

		if ($link) {
			$this->sendTextAnswer($message, sprintf('👤 К вашему аккаунт привязан профиль **%s**.', $link->user->userProfile->mediumName));
		} else {
			$builder = TelegramMessageAnswerBuilder::create()
			                                       ->setText('❕ Для связывания аккаунта воспользуйтесь личным кабинетом.')
			                                       ->addInlineKeyboardButton(TelegramInlineKeyboardBuilder::link('↗️ Перейти в CRM', $this->linkGenerator->generate('account.integrations'))->toArray());

			$this->sendAnswer($message, $builder->toArray());
		}
	}

	/**
	 * @throws ModelNotFoundException
	 */
	private function linkByCode(string $code, TMessage $message): UserTelegramLink
	{
		$dto = new TelegramUserDataDto([
			'telegramUserId' => $message->from->id,
			'telegramChatId' => $message->chat->id,
			'firstName'      => $message->from->first_name,
			'lastName'       => $message->from->last_name,
			'username'       => $message->from->username,
		]);

		return $this->linker->consumeTicket($code, $dto);
	}

	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 */
	private function revokeByTelegramId(int $telegramId): UserTelegramLink
	{
		$link = $this->linkRepository->findActiveByTelegramUserIdOrThrow($telegramId);

		$this->userTelegramLinkService->revoke($link);

		return $link;
	}

	/**
	 * @throws Exception
	 */
	private function sendAnswer(TMessage $message, array $config): void
	{
		$this->bot->send($message->chat->id, $config);
	}

	/**
	 * @throws Exception
	 */
	private function sendTextAnswer(TMessage $message, string $text, array $params = []): void
	{
		$this->bot->sendMessage($message->chat->id, $text, $params);
	}
}
