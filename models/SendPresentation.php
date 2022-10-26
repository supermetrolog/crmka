<?php

namespace app\models;

use app\helpers\validators\IsArrayValidator;
use app\models\letter\LetterWay;
use yii\base\Model;

class SendPresentation  extends Model
{

    public $emails;
    public $phones;

    public $comment;
    public $subject;
    public $offers;
    public $wayOfSending;
    public $letter_id;
    public $user_id;
    public function rules()
    {
        return [
            [['offers', 'wayOfSending', 'user_id', 'letter_id'], 'required'],
            [['user_id', 'letter_id'], 'integer'],
            [['subject'], 'string'],
            [['offers', 'wayOfSending'], IsArrayValidator::class],
            ['offers', 'validateOffers'],
            ['wayOfSending', 'validateWayOfSending'],
            [['comment', 'emails', 'phones'], 'safe']
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
        }
    }
    public function validateWayOfSending()
    {
        if (
            in_array(LetterWay::WAY_SMS, $this->wayOfSending) ||
            in_array(LetterWay::WAY_TELEGRAM, $this->wayOfSending) ||
            in_array(LetterWay::WAY_VIBER, $this->wayOfSending) ||
            in_array(LetterWay::WAY_WHATSAPP, $this->wayOfSending)
        ) {
            if (!$this->phones) {
                $this->addError('contacts', 'must be contain phone');
            }
        }
        // Пока работает только отправка по почте, необходимо иметь только почту
        if (in_array(LetterWay::WAY_EMAIL, $this->wayOfSending)) {
            if (!$this->emails) {
                $this->addError('contacts', 'must be contain emails');
            }
        } else {
            $this->addError('wayOfSending', 'must contain email contact type, the rest are not supported yet');
        }
    }
    public function validateContacts()
    {
        if ($this->emails == null && $this->phones == null) {
            $this->addError('contacts', 'must be contain either email or phone');
        }
    }
}
