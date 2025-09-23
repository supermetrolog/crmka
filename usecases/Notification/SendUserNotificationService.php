<?php

declare(strict_types=1);

namespace app\usecases\Notification;

use app\components\Notification\Builders\NotificationActionBuilder;
use app\components\Notification\Factories\NotifierFactory;
use app\components\Notification\Notification;
use app\components\Notification\NotificationAction;
use app\dto\UserNotification\SendUserNotificationDto;
use app\dto\UserNotification\UserNotificationActionDto;
use app\helpers\ArrayHelper;
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
	protected NotificationActionBuilder          $actionBuilder;

	public function __construct(
		NotifierFactory $notifierFactory,
		UserRepository $userRepository,
		TransactionBeginnerInterface $transactionBeginner,
		UserNotificationTemplateRepository $templateRepository,
		NotificationActionBuilder $actionBuilder
	)
	{
		$this->notifierFactory     = $notifierFactory;
		$this->userRepository      = $userRepository;
		$this->transactionBeginner = $transactionBeginner;
		$this->templateRepository  = $templateRepository;
		$this->actionBuilder       = $actionBuilder;
	}

	/**
	 * @param UserNotificationActionDto[] $actionDtos
	 *
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws \Throwable
	 * @throws ErrorException
	 * @throws InvalidConfigException
	 * @throws NotInstantiableException
	 */
	public function send(SendUserNotificationDto $dto, array $actionDtos = []): UserNotification
	{
		$user = $this->userRepository->findOneOrThrow($dto->userId);

		$notifier = $this->notifierFactory->create();

		$template = $dto->templateId ? $this->templateRepository->findOneOrThrow($dto->templateId) : null;

		$actions = ArrayHelper::map($actionDtos, fn(UserNotificationActionDto $dto) => $this->makeAction($dto));

		$notification = new Notification($dto->subject, $dto->message, $template, $actions, []);

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
	 * @param SendUserNotificationDto[]   $dtos
	 * @param UserNotificationActionDto[] $actionDtos
	 */
	public function sendAll(array $dtos, array $actionDtos): void
	{
		$this->transactionBeginner->run(function () use ($dtos, $actionDtos) {
			foreach ($dtos as $dto) {
				$this->send($dto, $actionDtos);
			}
		});
	}

	private function makeAction(UserNotificationActionDto $dto): NotificationAction
	{
		$builder = $this->actionBuilder::type($dto->type)
		                               ->label($dto->label)
		                               ->code($dto->code)
		                               ->order($dto->order);

		if ($dto->icon) {
			$builder->icon($dto->icon);
		}

		if ($dto->style) {
			$builder->style($dto->style);
		}

		if ($dto->confirmation) {
			$builder->confirm();
		}

		if ($dto->payload) {
			$builder->payload($dto->payload);
		}

		return $builder->build();
	}
}