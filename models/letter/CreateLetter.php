<?php

namespace app\models\letter;

use app\exceptions\ValidationErrorHttpException;
use app\helpers\validators\IsArrayValidator;
use Yii;

class CreateLetter extends Letter
{
    public $offers;
    public $contacts;
    public $ways;
    public function rules()
    {
        $rules = [
            [['offers', 'contacts', 'ways'], 'required'],
            [['offers', 'contacts', 'ways'], IsArrayValidator::class],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function create($postData)
    {
        $this->load($postData);
        if (!$this->validate()) {
            throw new ValidationErrorHttpException($this->getErrorSummary(false));
        }
        $tx = Yii::$app->db->beginTransaction();
        try {
            $letterID = $this->createLetter($this->getAttributes());
            $this->offers['letter_id'] = $letterID;
            $this->contacts['letter_id'] = $letterID;
            $this->ways['letter_id'] = $letterID;
            $this->createLetterOffers($this->offers);
            $this->createLetterContacts($this->contacts);
            $this->createLetterWays($this->ways);
            $tx->commit();
        } catch (\Throwable $th) {
            $tx->rollBack();
            throw $th;
        }
    }

    private function createLetter($data): int
    {
        $model = new Letter($data);
        if (!$model->load($data) || !$model->save())
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        return $model->id;
    }
    private function createLetterOffers(array $offers): void
    {
        foreach ($offers as $offer) {
            $model = new LetterOffer();
            if (!$model->load($offer) || !$model->save())
                throw new ValidationErrorHttpException($model->getErrorSummary(false));
        }
    }
    private function createLetterWays(array $ways): void
    {
        foreach ($ways as $way) {
            $model = new LetterWay();
            if (!$model->load($way) || !$model->save())
                throw new ValidationErrorHttpException($model->getErrorSummary(false));
        }
    }
    private function createLetterContacts(array $contacts): void
    {
        foreach ($contacts as $contact) {
            $model = new LetterContact();
            if (!$model->load($contact) || !$model->save())
                throw new ValidationErrorHttpException($model->getErrorSummary(false));
        }
    }
}
