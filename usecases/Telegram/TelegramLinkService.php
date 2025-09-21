<?php
declare(strict_types=1);

namespace app\usecases\Telegram;

use app\components\Telegram\Interfaces\TelegramDeepLinkGeneratorInterface;
use app\components\Telegram\TelegramBotApiClient;
use app\dto\Telegram\StartLinkTelegramDto;
use app\dto\Telegram\StatusLinkTelegramDto;
use app\dto\Telegram\TelegramUserDataDto;
use app\dto\User\UserTelegramLinkDto;
use app\dto\User\UserTelegramLinkTicketDto;
use app\exceptions\services\Telegram\UserTelegramTicketIsConsumedException;
use app\exceptions\services\Telegram\UserTelegramTicketIsExpiredException;
use app\helpers\DateIntervalHelper;
use app\helpers\DateTimeHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\User\User;
use app\models\User\UserTelegramLink;
use app\repositories\UserTelegramLinkRepository;
use app\repositories\UserTelegramLinkTicketRepository;
use Exception;
use yii\base\Security;

final class TelegramLinkService
{
	protected const TICKET_TTL = 60 * 10;

	protected Security                           $security;
	protected UserTelegramLinkRepository         $repository;
	protected UserTelegramLinkTicketService      $ticketService;
	protected TelegramDeepLinkGeneratorInterface $deepLinkGenerator;
	protected UserTelegramLinkTicketRepository   $ticketRepository;
	protected TransactionBeginnerInterface       $transactionBeginner;
	protected UserTelegramLinkService            $linkService;
	protected TelegramBotApiClient               $bot;

	public function __construct(
		Security $security,
		UserTelegramLinkRepository $repository,
		UserTelegramLinkTicketService $ticketService,
		TelegramDeepLinkGeneratorInterface $deepLinkGenerator,
		UserTelegramLinkTicketRepository $ticketRepository,
		TransactionBeginnerInterface $transactionBeginner,
		UserTelegramLinkService $linkService,
		TelegramBotApiClient $bot
	)
	{
		$this->security            = $security;
		$this->repository          = $repository;
		$this->ticketService       = $ticketService;
		$this->deepLinkGenerator   = $deepLinkGenerator;
		$this->ticketRepository    = $ticketRepository;
		$this->transactionBeginner = $transactionBeginner;
		$this->linkService         = $linkService;
		$this->bot                 = $bot;
	}

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 */
	public function createTicket(User $user): StartLinkTelegramDto
	{
		$ticket = $this->ticketRepository->findActiveByUserId($user->id);

		if (!$ticket) {
			$ticket = $this->ticketService->create(new UserTelegramLinkTicketDto([
				'userId'    => $user->id,
				'code'      => $this->security->generateRandomString(16),
				'expiresAt' => DateTimeHelper::now()->add(DateIntervalHelper::seconds(self::TICKET_TTL)),
			]));
		}

		$deeplink = $this->deepLinkGenerator->forTicket($ticket->code);

		return new StartLinkTelegramDto([
			'deepLink'  => $deeplink,
			'code'      => $ticket->code,
			'expiresAt' => $ticket->expires_at
		]);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws Exception
	 */
	public function consumeTicket(string $code, TelegramUserDataDto $dto): UserTelegramLink
	{
		$ticket = $this->ticketRepository->findByCodeOrThrow($code);

		if ($ticket->isExpired()) {
			throw new UserTelegramTicketIsExpiredException();
		}

		if ($ticket->isConsumed()) {
			throw new UserTelegramTicketIsConsumedException();
		}

		return $this->transactionBeginner->run(function () use ($ticket, $dto) {
			$activeLink = $this->repository->findActiveByUserId($ticket->user_id);

			if ($activeLink) {
				$this->linkService->revoke($activeLink);
			}

			$link = $this->linkService->create(new UserTelegramLinkDto([
				'userId'         => $ticket->user_id,
				'telegramUserId' => $dto->telegramUserId,
				'chatId'         => $dto->telegramChatId,
				'username'       => $dto->username,
				'firstName'      => $dto->firstName,
				'lastName'       => $dto->lastName,
				'isEnabled'      => true
			]));

			$this->ticketService->consume($ticket);

			return $link;
		});
	}

	public function getStatusForUser(User $user): StatusLinkTelegramDto
	{
		$link = $this->repository->findActiveByUserId($user->id);

		return new StatusLinkTelegramDto([
			'linked'         => (bool)$link,
			'username'       => $link->username ?? null,
			'isLoginEnabled' => $link->is_enabled ?? false,
			'firstName'      => $link->first_name ?? null,
			'lastName'       => $link->last_name ?? null
		]);
	}

	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 */
	public function revokeByUser(User $user): void
	{
		$link = $this->repository->findActiveByUserIdOrThrow($user->id);

		$this->linkService->revoke($link);
	}

	/**
	 * @throws SaveModelException
	 */
	public function revoke(UserTelegramLink $link): void
	{
		$this->linkService->revoke($link);
	}
}
