<?php

declare(strict_types=1);

namespace app\usecases\Notification;

use app\components\Notification\Factories\NotifierFactory;
use app\components\Notification\Notification;
use app\dto\UserNotification\SendUserNotificationDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Notification\UserNotification;
use app\repositories\UserNotificationTemplateRepository;
use app\repositories\UserRepository;
use ErrorException;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class SendUserNotificationService
{
	protected NotifierFactory                    $notifierFactory;
	protected UserRepository                     $userRepository;
	protected TransactionBeginnerInterface       $transactionBeginner;
	protected UserNotificationTemplateRepository $templateRepository;

	public function __construct(
		NotifierFactory $notifierFactory,
		UserRepository $userRepository,
		TransactionBeginnerInterface $transactionBeginner,
		UserNotificationTemplateRepository $templateRepository
	)
	{
		$this->notifierFactory     = $notifierFactory;
		$this->userRepository      = $userRepository;
		$this->transactionBeginner = $transactionBeginner;
		$this->templateRepository  = $templateRepository;
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws \Throwable
	 * @throws ErrorException
	 * @throws InvalidConfigException
	 * @throws NotInstantiableException
	 */
	public function send(SendUserNotificationDto $dto): UserNotification
	{
		$user = $this->userRepository->findOneOrThrow($dto->userId);

		$notifier = $this->notifierFactory->create();

		$template = $dto->templateId ? $this->templateRepository->findOneOrThrow($dto->templateId) : null;

		$notification = new Notification($dto->subject, $dto->message, $template, [], []);

		$notifier->setNotifiable($user)
		         ->setNotification($notification)
		         ->setSendNow(true)
		         ->setChannel($dto->channel);

		if ($dto->createdById) {
			$notifier->setCreatedById($dto->createdById)->setCreatedByType($dto->createdByType);
		}

		return $notifier->send();
	}

	/**
	 * @param SendUserNotificationDto[] $dtos
	 */
	public function sendAll(array $dtos): void
	{
		$this->transactionBeginner->run(function () use ($dtos) {
			foreach ($dtos as $dto) {
				$this->send($dto);
			}
		});
	}
}