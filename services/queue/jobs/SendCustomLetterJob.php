<?php

namespace app\services\queue\jobs;

use app\events\NotificationEvent;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\letter\Letter;
use app\models\Notification;
use app\models\User;
use app\services\emailsender\EmailSender;
use RuntimeException;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\queue\amqp_interop\Queue;
use yii\queue\JobInterface;

class SendCustomLetterJob extends BaseObject implements JobInterface
{
	public int    $letter_id;
	public int    $user_id;
	public array  $emails;
	public string $subject;
	public string $body;
	public array  $ways;
	public bool   $showSignature = false;

	/**
	 * @param Queue $queue
	 *
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 */
	public function execute($queue): void
	{
		try {
			/** @var User $user */
			$user = User::find()
			            ->with(['userProfile'])
			            ->byId($this->user_id)
			            ->oneOrThrow();

			$emailSender = new EmailSender([
				'emails'   => $this->emails,
				'from'     => $user->getEmailForSend(),
				'view'     => 'presentation/index',
				'viewArgv' => [
					'userMessage'   => $this->body,
					'showSignature' => $this->showSignature,
					'user'          => $user
				],
				'subject'  => $this->subject,
				'username' => $user->getEmailUsername(),
				'password' => $user->getEmailPassword(),
			]);

			$emailSender->validate();

			if ($emailSender->hasErrors()) {
				throw new RuntimeException("EmailSender validation error: " . implode(', ', $emailSender->getErrorSummary(false)));
			}

			if (!$emailSender->send()) {
				throw new RuntimeException("Email send error");
			}

			$this->changeLetterStatus(Letter::STATUS_SUCCESS);
		} catch (Throwable $th) {
			$this->notifyUserAboutError($th->getMessage());
			$this->changeLetterStatus(Letter::STATUS_ERROR);

			Yii::warning([$this->emails, $user->getEmailUsername(), $user->getEmailPassword(), $user->getEmailForSend()]);

			throw $th;
		}
	}

	private function notifyUserAboutError(string $error): void
	{
		Yii::$app->notify->notifyUser(
			new NotificationEvent([
				'consultant_id' => $this->user_id,
				'type'          => Notification::TYPE_SYSTEM_DANGER,
				'title'         => 'Ошибка отправки письма',
				'body'          => "Ошибка отправки письма: {$error}. По контактам: " . implode(', ', $this->emails)
			])
		);
	}

	private function changeLetterStatus(int $status): void
	{
		/** @var Letter $model */
		$model = Letter::findOne($this->letter_id);

		if ($model) {
			$model->updateAttributes(['status' => $status]);
		}
	}
}
