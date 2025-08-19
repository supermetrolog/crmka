<?php

namespace app\services\queue\jobs;

use app\events\NotificationEvent;
use app\models\letter\Letter;
use app\models\Notification;
use app\models\User;
use app\services\emailsender\EmailSender;
use RuntimeException;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

class SendCustomLetterJob extends BaseObject implements JobInterface
{
    public int $letter_id;
    public int $user_id;
    public array $emails;
    public string $subject;
    public string $body;
    public array $ways;

    /**
     * @param Queue $queue
     *
     * @throws Throwable
     */
    public function execute($queue): void
    {
        try {
            /** @var User $user */
            $user = User::find()->with(['userProfile'])->where(['id' => $this->user_id])->limit(1)->one();

            if (!$user) {
                throw new RuntimeException("User not found");
            }

            $data = [
                'emails'   => $this->getEmails($user),
                'from'     => $user->getEmailForSend(),
                'view'     => 'presentation/index',
                'viewArgv' => [
                    'userMessage' => $this->body
                ],
                'subject'  => $this->subject,
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

            $this->changeLetterStatus(Letter::STATUS_SUCCESS);
        } catch (Throwable $th) {
            $this->notifyUserAboutError($th->getMessage());
            $this->changeLetterStatus(Letter::STATUS_ERROR);
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
            $model->status = $status;
            $model->save(false);
        }
    }

    private function getEmails(User $user): array
    {
        if ($user->email) {
            return array_merge($this->emails, [$user->email]);
        }

        return $this->emails;
    }
}
