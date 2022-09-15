<?php


namespace app\helpers\validators;

use yii\validators\Validator;

class IsArrayValidator extends Validator
{

    public function validateAttribute($model, $attr)
    {
        if (!is_array($model->$attr)) {
            $this->addError($model, $attr, '"{attribute}" must be array');
        }
    }
}
