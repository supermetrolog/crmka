<?php

namespace app\components;

use app\events\NotificationEvent;
use app\events\SendMessageEvent;
use app\exceptions\ValidationErrorHttpException;
use app\models\Notification;
use app\models\UserSendedData;
use Exception;
use Yii;
use yii\base\Component;

class NotificationService  extends Component
{
    public function notifyUser(NotificationEvent $event)
    {
        $model = new Notification();

        $model->consultant_id = $event->consultant_id;
        $model->type = $event->type;
        $model->title = $event->title;
        $model->body = $event->body;
        $model->status = Notification::NO_FETCHED_STATUS;
        if ($model->save()) {
            return true;
        }

        throw new ValidationErrorHttpException($model->getErrorSummary(false));
    }
    private function normalizeContacts($contacts)
    {
        $newContacts = [];
        foreach ($contacts as $contact) {
            if (str_contains($contact, '@')) {
                $newContacts['emails'][] = $contact;
            } else {
                preg_match_all('!\d+!', $contact, $numbers);
                $phone = implode('', $numbers[0]);
                $newContacts['phones'][] = $phone;
            }
        }

        return $newContacts;
    }
    public function sendMessage(SendMessageEvent $event)
    {
        $event->contacts = $this->normalizeContacts($event->contacts);
        $isSended = false;
        if (in_array(UserSendedData::EMAIL_CONTACT_TYPE, $event->wayOfSending)) {
            $this->sendEmails($event);
            $isSended = true;
        }
        /**
          Todo: Developed sendPhones features!
         */

        if (!$isSended) {
            throw new Exception('Message not sended');
        }
    }

    private function sendEmails(SendMessageEvent $event)
    {
        foreach ($event->contacts['emails'] as $contact) {
            if ($event->notSend) {
                $this->saveUserSendedData($event, $contact, UserSendedData::EMAIL_CONTACT_TYPE);
                continue;
            }
            $message = Yii::$app->mailer->compose()
                ->setFrom($event->from)
                ->setTo($contact)
                ->setSubject($event->subject)
                ->setHtmlBody($event->htmlBody);

            if (!$message->send()) {
                throw new Exception('Message not sended');
            }
            $this->saveUserSendedData($event, $contact, UserSendedData::EMAIL_CONTACT_TYPE);
        }
    }

    private function saveUserSendedData(SendMessageEvent $event, $contact, $contact_type)
    {
        $model = new UserSendedData([
            'user_id' => $event->user_id,
            'contact' => $contact,
            'contact_type' => $contact_type,
            'type' => $event->type,
            'description' => $event->description
        ]);

        if (!$model->save()) {
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        }
    }
}
