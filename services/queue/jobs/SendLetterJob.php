<?php

namespace app\services\queue\jobs;

use app\events\NotificationEvent;
use app\exceptions\ValidationErrorHttpException;
use app\helpers\ArrayHelper;
use app\models\letter\Letter;
use app\models\Notification;
use app\models\SendPresentation;
use app\models\User;
use app\services\emailsender\EmailSender;
use RuntimeException;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

class SendLetterJob extends BaseObject implements JobInterface
{
	public SendPresentation $model;

	/**
	 * @throws ValidationErrorHttpException
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);

		$this->model->validate();

		if ($this->model->hasErrors()) {
			throw new ValidationErrorHttpException($this->model->getErrorSummary(false));
		}
	}

	/**
	 * @param Queue $queue
	 *
	 * @throws Throwable
	 */
	public function execute($queue): void
	{
		try {
			/** @var User $user */
			$user = User::find()->with(['userProfile'])->where(['id' => $this->model->user_id])->limit(1)->one();

			$data = [
				'emails'   => $this->getEmails($user),
				'from'     => $user->getEmailForSend(),
				'view'     => 'presentation/index',
				'viewArgv' => [
					'userMessage' => $this->model->comment
				],
				'subject'  => $this->model->subject,
				'username' => $user->getEmailUsername(),
				'password' => $user->getEmailPassword(),
			];

			$emailSender = new EmailSender();

			$emailSender->load($data);
			$emailSender->validate();

			if ($emailSender->hasErrors()) {
				throw new RuntimeException("EmailSender validation error: " . implode(', ', $emailSender->getErrorSummary(false)));
			}

			if (!$emailSender->send()) {
				throw new RuntimeException("Email send error");
			}
		} catch (Throwable $th) {
			$this->notifyUserAboutError($th->getMessage());

			$this->changeLetterStatus(Letter::STATUS_ERROR);

			throw $th;
		}
	}

	private function notifyUserAboutError($error): void
	{
		Yii::$app->notify->notifyUser(
			new NotificationEvent([
				'consultant_id' => $this->model->user_id,
				'type'          => Notification::TYPE_SYSTEM_DANGER,
				'title'         => 'ошибка',
				'body'          => "Ошибка отправки презентаций: {$error}. По контактам: " . implode(', ', $this->model->emails)
			])
		);
	}

	private function changeLetterStatus(int $status): void
	{
		/** @var Letter $model */
		$model = Letter::findOne($this->model->letter_id);

		$model->status = $status;
		$model->save(false);
	}

	private function getEmails(User $user): array
	{
		if ($user->email) {
			return ArrayHelper::merge($this->model->emails, [$user->email]);
		}

		return $this->model->emails;
	}
}
