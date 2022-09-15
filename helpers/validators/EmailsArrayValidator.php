<?php


namespace app\helpers\validators;

use yii\validators\EmailValidator;
use yii\validators\Validator;

class EmailsArrayValidator extends Validator
{

    public function validateAttribute($model, $attr)
    {
        if (!is_array($model->$attr)) {
        }
        if (!is_array($model->$attr) || !count($model->$attr)) {
            return;
        }
        $validator = new EmailValidator();
        foreach ($model->$attr as $email) {
            if (!$validator->validate($email)) {
                $this->addError($model, $attr, '"{attribute}" contain invalid email');
            }
        }
    }
}
