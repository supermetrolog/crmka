<?php

namespace app\models;

use app\models\UserSendedData;
use app\services\queue\jobs\TestJob;
use Yii;
use yii\base\Model;

class SendPresentation  extends Model
{

    public $contacts;
    public $emails;
    public $phones;

    public $comment;
    public $offers;
    public $sendClientFlag;
    public $step;
    public $wayOfSending;

    public $user_id;

    public function rules()
    {
        return [
            [['contacts', 'offers', 'sendClientFlag', 'step', 'wayOfSending', 'user_id'], 'required'],
            [['user_id'], 'integer'],
            ['contacts', 'validateContacts'],
            ['offers', 'validateOffers'],
            ['wayOfSending', 'validateWayOfSending'],
        ];
    }
    private function checkArrayField($array, $key)
    {
        if (!is_array($array)) {
            return false;
        }

        if (!key_exists($key, $array)) {
            return false;
        }

        if ($array[$key] == null) {
            return false;
        }
        return true;
    }
    public function validateOffers()
    {
        if (!is_array($this->offers)) {
            $this->addError('offers', "must be array");
        }
        if ($this->offers == null) {
            $this->addError("offers", 'not be null');
        }

        if (!count($this->offers)) {
            $this->addError("offers", 'not be empty');
        }
        foreach ($this->offers as $offer) {
            if (!$this->checkArrayField($offer, 'object_id')) {
                $this->addError('offers', 'object_id not be empty');
            }
            if (!$this->checkArrayField($offer, 'original_id')) {
                $this->addError('offers', 'original_id not be empty');
            }
            if (!$this->checkArrayField($offer, 'type_id')) {
                $this->addError('offers', 'type_id not be empty');
            }
            if (!$this->checkArrayField($offer, 'consultant')) {
                $this->addError('offers', 'consultant not be empty');
            }
        }
    }
    public function validateWayOfSending()
    {
        if (!is_array($this->wayOfSending)) {
            $this->addError('wayOfSending', "must be array");
        }
        if ($this->wayOfSending == null) {
            $this->addError("wayOfSending", 'not be null');
        }

        if (!count($this->wayOfSending)) {
            $this->addError("wayOfSending", 'not be empty');
        }
        // Пока работает только отправка по почте, необходимо иметь только почту
        if (in_array(UserSendedData::EMAIL_CONTACT_TYPE, $this->wayOfSending)) {
            if (!$this->emails) {
                $this->addError('contacts', 'must be contain emails');
            }
        } else {
            $this->addError('wayOfSending', 'must contain email contact type, the rest are not supported yet');
        }
    }
    private static function normalizeContacts($contacts)
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
                if ($phone != '') {
                    $newContacts['phones'][] = $phone;
                }
            }
        }
        if (!count($newContacts['emails']) && !count($newContacts['phones'])) {
            return false;
        }
        return $newContacts;
    }
    public function load($data, $formName = null)
    {
        parent::load($data, $formName);
        if (is_array($this->contacts) && count($this->contacts)) {
            $contacts = $this->normalizeContacts($this->contacts);
            if ($contacts) {
                $this->emails =  $contacts['emails'];
                $this->phones =  $contacts['phones'];
            }
        }
    }
    public function validateContacts()
    {
        if (!is_array($this->contacts)) {
            $this->addError('contacts', "must be array");
        }
        if ($this->contacts == null) {
            $this->addError("contacts", 'not be null');
        }

        if (!count($this->contacts)) {
            $this->addError("contacts", 'not be empty');
        }

        if ($this->emails == null && $this->phones == null) {
            $this->addError('contacts', 'must be contain either email or phone');
        }
    }
    public function send()
    {
        $q = Yii::$app->queue;
        $q->push(new TestJob([
            'text' => "Fuck the police"
        ]));
    }
}
