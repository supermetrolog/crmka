<?php

namespace app\models\letter;

use app\exceptions\ValidationErrorHttpException;
use app\helpers\validators\IsArrayValidator;
use Yii;
use yii\validators\RequiredValidator;

class CreateLetter extends Letter
{
    public $offers;
    public $contacts;
    public $ways;

    public Letter $letterModel;

    public function rules()
    {
        return  [
            [['ways', 'contacts'], 'required'],
            [['offers', 'ways'], IsArrayValidator::class],
            ['contacts', 'validateContacts'],
            ['ways', 'validateWays']
        ];
    }
    public function validateWays()
    {
        if ($this->hasErrors())
            return;
        if (
            in_array(LetterWay::WAY_SMS, $this->ways) ||
            in_array(LetterWay::WAY_TELEGRAM, $this->ways) ||
            in_array(LetterWay::WAY_VIBER, $this->ways) ||
            in_array(LetterWay::WAY_WHATSAPP, $this->ways)
        ) {
            if (!$this->contacts['phones']) {
                $this->addError('contacts', 'must be contain phone');
            }
        }
        if (in_array(LetterWay::WAY_EMAIL, $this->ways)) {
            if (!$this->contacts['emails']) {
                $this->addError('contacts', 'must be contain emails');
            }
        } else {
            $this->addError('ways', 'must contain email contact type, the rest are not supported yet');
        }
    }
    private function checkArrayField($array, $key)
    {
        if (!is_array($array)) {
            return false;
        }

        if (!key_exists($key, $array)) {
            return false;
        }
        if ($array[$key] === null) {
            return false;
        }
        return true;
    }
    public function validateContacts()
    {
        $required = new RequiredValidator();
        if (
            !$this->checkArrayField($this->contacts, 'emails') ||
            !$this->checkArrayField($this->contacts, 'phones') ||
            (!$required->validate($this->contacts['phones']) && !$required->validate($this->contacts['emails']))
        ) {
            $this->addError('contacts', 'emails or phones not be empty');
        }
    }

    public function create($postData): Letter
    {
        if (!$this->load($postData, '') || !$this->validate()) {
            throw new ValidationErrorHttpException($this->getErrorSummary(false));
        }
        $tx = Yii::$app->db->beginTransaction();
        try {
            $letterModel = $this->createLetter($postData);
            $this->createLetterOffers($this->offers, $letterModel->id);
            $this->createLetterContacts($this->contacts, $letterModel->id);
            $this->createLetterWays($this->ways, $letterModel->id);
            $tx->commit();
        } catch (\Throwable $th) {
            $tx->rollBack();
            throw $th;
        }
        $this->letterModel = $letterModel;
        return $letterModel;
    }

    private function createLetter($data): Letter
    {
        $model = new Letter();
        if (!$model->load($data, '') || !$model->save())
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        return $model;
    }
    private function createLetterOffers(array $offers, int $letter_id): void
    {
        foreach ($offers as $offer) {
            $offer['letter_id'] = $letter_id;
            $model = new LetterOffer();
            if (!$model->load($offer, '') || !$model->save())
                throw new ValidationErrorHttpException($model->getErrorSummary(false));
        }
    }
    private function createLetterWays(array $ways, int $letter_id): void
    {
        foreach ($ways as $way) {
            $model = new LetterWay();
            $config = [
                'way' => $way,
                'letter_id' => $letter_id
            ];
            if (!$model->load($config, '') || !$model->save())
                throw new ValidationErrorHttpException($model->getErrorSummary(false));
        }
    }
    private function createLetterContacts(array $contacts, int $letter_id): void
    {
        foreach ($contacts['emails'] as $email) {
            $model = new LetterContact();
            $config = [
                'letter_id' => $letter_id,
                'email' => $email['value'],
                'contact_id' => $email['contact_id']
            ];
            if (!$model->load($config, '') || !$model->save())
                throw new ValidationErrorHttpException($model->getErrorSummary(false));
        }

        foreach ($contacts['phones'] as $phone) {
            $model = new LetterContact();
            $config = [
                'letter_id' => $letter_id,
                'phone' => $phone['value'],
                'contact_id' => $phone['contact_id']
            ];
            if (!$model->load($config, '') || !$model->save())
                throw new ValidationErrorHttpException($model->getErrorSummary(false));
        }
    }
}
