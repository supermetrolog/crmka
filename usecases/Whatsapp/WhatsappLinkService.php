<?php
declare(strict_types=1);

namespace app\usecases\Whatsapp;

use app\components\Integrations\Whatsapp\WhatsappApiClient;
use app\dto\User\UserWhatsappLinkDto;
use app\dto\Whatsapp\StatusLinkWhatsappDto;
use app\enum\Phone\PhoneCountryCodeEnum;
use app\exceptions\services\Whatsapp\WhatsappPhoneNotExistsException;
use app\helpers\PhoneHelper;
use app\helpers\TypeConverterHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\User\User;
use app\models\User\UserWhatsappLink;
use app\repositories\UserWhatsappLinkRepository;
use yii\base\Security;

final class WhatsappLinkService
{
	protected Security                     $security;
	protected UserWhatsappLinkRepository   $repository;
	protected TransactionBeginnerInterface $transactionBeginner;
	protected UserWhatsappLinkService      $linkService;
	protected WhatsappApiClient            $whatsappApi;

	public function __construct(
		WhatsappApiClient $whatsappApi,
		UserWhatsappLinkService $linkService,
		TransactionBeginnerInterface $transactionBeginner,
		UserWhatsappLinkRepository $repository
	)
	{
		$this->whatsappApi         = $whatsappApi;
		$this->linkService         = $linkService;
		$this->transactionBeginner = $transactionBeginner;
		$this->repository          = $repository;
	}

	public function link(User $user, int $phone): UserWhatsappLink
	{
		return $this->transactionBeginner->run(function () use ($user, $phone) {
			$activeLink = $this->repository->findActiveByUserId($user->id);

			if ($activeLink) {
				$this->linkService->revoke($activeLink);
			}

			$contactResponse = $this->whatsappApi->checkPhone($phone);

			if (!$contactResponse->on_whatsapp) {
				throw new WhatsappPhoneNotExistsException();
			}

			$info = $this->whatsappApi->getContactInfo($phone);

			$link = $this->linkService->create(new UserWhatsappLinkDto([
				'userId'            => $user->id,
				'whatsappProfileId' => $info->profile->id,
				'phone'             => TypeConverterHelper::toString($phone),
				'firstName'         => $info->profile->contact->Found ? $info->profile->contact->FirstName : null,
				'fullName'          => $info->profile->contact->Found ? $info->profile->contact->FullName : null,
				'pushName'          => $info->profile->contact->Found ? $info->profile->contact->PushName : null
			]));

			$this->whatsappApi->sendMessage(TypeConverterHelper::toString($phone), sprintf('✅ Ваш аккаунт связан с CRM профилем "%s".', $link->user->userProfile->mediumName));

			return $link;
		});
	}

	public function getStatusForUser(User $user): StatusLinkWhatsappDto
	{
		$link = $this->repository->findActiveByUserId($user->id);

		return new StatusLinkWhatsappDto([
			'linked'    => (bool)$link,
			'firstName' => $link->first_name ?? null,
			'fullName'  => $link->full_name ?? null,
			'pushName'  => $link->push_name ?? null,
			'phone'     => $link ? PhoneHelper::tryFormat($link->phone, PhoneHelper::FORMAT_NATIONAL, PhoneCountryCodeEnum::RU) : null
		]);
	}

	public function revokeByUser(User $user): void
	{
		$this->transactionBeginner->run(function () use ($user) {
			$link = $this->repository->findActiveByUserIdOrThrow($user->id);

			$this->linkService->revoke($link);

			$this->whatsappApi->sendMessage($link->phone, sprintf('☑ Ваш аккаунт отвязан от CRM профиля "%s".', $link->user->userProfile->mediumName));
		});
	}

	/**
	 * @throws SaveModelException
	 */
	public function revoke(UserWhatsappLink $link): void
	{
		$this->linkService->revoke($link);
	}
}
