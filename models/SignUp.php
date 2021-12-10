<?php

namespace app\models;

use yii\base\Model;
use app\exceptions\ValidationErrorHttpException;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user This property is read-only.
 *
 */
class SignUp extends Model
{
    public $username;
    public $password;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            ['password', 'string', 'min' => 4],
            ['username', 'string', 'min' => 4],
            // password is validated by validatePassword()
            ['username', 'validateUsername'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateUsername($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if ($user) {
                $this->addError($attribute, 'Пользователь с таким username уже существует.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function signUp()
    {
        if ($this->validate()) {
            $user = new User();
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->username = $this->username;
            $user->created_at = time();
            $user->updated_at = time();
            if ($user->validate() && $user->save()) return $user->id;
            throw new ValidationErrorHttpException($user->getErrorSummary(false));
        }
        throw new ValidationErrorHttpException($this->getErrorSummary(false));
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
