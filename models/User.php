<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\filters\auth\HttpBearerAuth;
use yii\data\ActiveDataProvider;
use app\exceptions\ValidationErrorHttpException;
use app\models\user\auth\Role;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property string $email_username
 * @property string $email_password
 * @property string|null $access_token
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * 
 * 
 * @property UserProfile[] $userProfiles
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'access_token', 'email_password', 'email_username'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'access_token' => 'Verification Token',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];
        return $behaviors;
    }


    public static function getUsers()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => self::find()->distinct()->with(['userProfile' => function ($query) {
                $query->with(['phones', 'emails']);
            }, 'role'])->where(['status' => self::STATUS_ACTIVE]),
            'pagination' => [
                'pageSize' => 0,
            ],
        ]);

        return $dataProvider;
    }
    public static function getUser($id)
    {
        return self::find()->distinct()->with(['userProfile' => function ($query) {
            $query->with(['phones', 'emails']);
        }])->where(['status' => self::STATUS_ACTIVE, 'id' => $id])->one();
    }
    public static function createUser($post_data, $uploadFileModel)
    {
        $db = Yii::$app->db;
        $model = new SignUp();
        $transaction = $db->beginTransaction();
        try {
            if ($model->load($post_data, '') && $user_id = $model->signUp()) {
                $post_data['userProfile']['user_id'] = $user_id;
                UserProfile::createUserProfile($post_data['userProfile'], $uploadFileModel);
                // $transaction->rollBack();
                // return $post_data;

                $transaction->commit();
                return ['message' => "Пользователь создан", 'data' => $user_id];
            }
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }
    public static function updateUser(User $user, $post_data, $uploadFileModel)
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $post_data['updated_at'] = time();

            if ($user->load($post_data, '')) {
                if (ArrayHelper::keyExists("password", $post_data) && $post_data['password'] != null) {
                    if (strlen($post_data['password']) < 5) {
                        throw new ValidationErrorHttpException(["Пароль должен быть больше 4-х символов"]);
                    }

                    $user->setPassword($post_data['password']);
                }
                if ($user->save()) {
                    UserProfile::updateUserProfile($post_data['userProfile'], $uploadFileModel);
                    $transaction->commit();
                    return ['message' => "Пользователь изменен", 'data' => $user->id];
                }
            }
            throw new ValidationErrorHttpException($user->getErrorSummary(false));
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }
    public function fields()
    {
        $fields = parent::fields();
        unset(
            $fields['auth_key'],
            $fields['password_hash'],
            $fields['password_reset_token'],
            $fields['access_token'],
            $fields['email_password']
        );
        $fields['created_at_format'] = function ($fields) {
            return Yii::$app->formatter->format($fields['created_at'], 'datetime');
        };
        $fields['updated_at_format'] = function ($fields) {
            return $fields['updated_at'] ? Yii::$app->formatter->format($fields['updated_at'], 'datetime') : null;
        };
        return $fields;
    }

    /**
     * Gets query for [[UserProfiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'id']);
    }


    /**
     * Gets query for [[UserProfiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['user_id' => 'id']);
    }


    /**
     * Gets query for [[Contacts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContacts()
    {
        return $this->hasMany(Contact::className(), ['user_id' => 'id']);
    }













    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     *  @return User 
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_ACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    /**
     * Generates new token for email verification
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
