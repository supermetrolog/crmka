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
        $newContacts = [
            'phones' => [],
            'emails' => []
        ];
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
            $this->sendEmails($event, UserSendedData::EMAIL_CONTACT_TYPE);
            $isSended = true;
        }
        if (in_array(UserSendedData::SMS_CONTACT_TYPE, $event->wayOfSending)) {
            $this->sendPhones($event, UserSendedData::SMS_CONTACT_TYPE);
            $isSended = true;
        }
        if (in_array(UserSendedData::TELEGRAM_CONTACT_TYPE, $event->wayOfSending)) {
            $this->sendPhones($event, UserSendedData::TELEGRAM_CONTACT_TYPE);
            $isSended = true;
        }
        if (in_array(UserSendedData::WHATSAPP_CONTACT_TYPE, $event->wayOfSending)) {
            $this->sendPhones($event, UserSendedData::WHATSAPP_CONTACT_TYPE);
            $isSended = true;
        }
        if (in_array(UserSendedData::VIBER_CONTACT_TYPE, $event->wayOfSending)) {
            $this->sendPhones($event, UserSendedData::VIBER_CONTACT_TYPE);
            $isSended = true;
        }
        /**
          Todo: Developed sendPhones features!
         */

        if (!$isSended) {
            throw new Exception('Message not sended');
        }
    }
    private function sendPhones(SendMessageEvent $event, $contact_type)
    {
        if (!count($event->contacts['phones'])) {
            throw new Exception('Чтобы отправить сообщение способом (' . UserSendedData::CONTACT_TYPES[$contact_type] . '), нужно выбрать номер телефона!');
        }
        foreach ($event->contacts['phones'] as $contact) {
            if ($event->notSend) {
                $this->saveUserSendedData($event, $contact, $contact_type);
                continue;
            }
            $this->saveUserSendedData($event, $contact, $contact_type);
        }
    }
    private function sendEmails(SendMessageEvent $event, $contact_type)
    {
        if (!count($event->contacts['emails'])) {
            throw new Exception('Чтобы отправить сообщение способом (' . UserSendedData::CONTACT_TYPES[$contact_type] . '), нужно выбрать Email!');
        }
        foreach ($event->contacts['emails'] as $contact) {
            if ($event->notSend) {
                $this->saveUserSendedData($event, $contact, UserSendedData::EMAIL_CONTACT_TYPE);
                continue;
            }
            $message = Yii::$app->mailer->compose($event->view, $event->viewArgv)
                ->setFrom($event->from)
                ->setTo($contact)
                ->setSubject($event->subject);

            if ($event->files) {
                foreach ($event->files as $file) {
                    $message->attach($file);
                }
            }
            if ($event->htmlBody) {
                $message->setHtmlBody($event->htmlBody);
            }

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
