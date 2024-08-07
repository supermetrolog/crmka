<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use Yii;
use app\exceptions\ValidationErrorHttpException;
use app\models\miniModels\UserProfileEmail;
use app\models\miniModels\UserProfilePhone;
use app\behaviors\CreateManyMiniModelsBehaviors;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user_profile".
 *
 * @property int $id
 * @property int $user_id [связь] с юзером
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property string|null $caller_id Номер в системе Asterisk
 * @property string|null $avatar
 * 
 * @property string $fullName
 * @property string $shortName
 * @property string $mediumName
 *
 * @property CallList[] $callLists
 * @property User $user
 */
class UserProfile extends AR
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['first_name', 'middle_name', 'last_name', 'caller_id', 'avatar'], 'string', 'max' => 255],
            [['caller_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            CreateManyMiniModelsBehaviors::class
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'caller_id' => 'Caller ID',
            'avatar' => "Avatar",
        ];
    }
    public function uploadFiles($uploadFileModel, UserProfile $model)
    {
        foreach ($uploadFileModel->files as $file) {
            if (!$uploadFileModel->uploadOne($file)) {
                throw new ValidationErrorHttpException($uploadFileModel->getErrorSummary(false));
            }
            $model->avatar = $uploadFileModel->filename;
        }
        return $model;
    }
    public static function createUserProfile($post_data, $uploadFileModel)
    {
        $model = new self();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model->load($post_data, '')) {
                $model = $model->uploadFiles($uploadFileModel, $model);
                if ($model->save()) {
                    $model->createManyMiniModels([
                        UserProfileEmail::class =>  ArrayHelper::getValue($post_data, 'emails'),
                        UserProfilePhone::class =>  ArrayHelper::getValue($post_data, 'phones'),
                    ]);
                    $transaction->commit();
                    return true;
                }
            }
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }
    public static function updateUserProfile($post_data, $uploadFileModel)
    {
        $model = self::findOne($post_data['id']);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model->load($post_data, '')) {
                $model = $model->uploadFiles($uploadFileModel, $model);
                if ($model->save()) {
                    $model->updateManyMiniModels([
                        UserProfileEmail::class =>  ArrayHelper::getValue($post_data, 'emails'),
                        UserProfilePhone::class =>  ArrayHelper::getValue($post_data, 'phones'),
                    ]);
                    $transaction->commit();
                    return true;
                }
            }
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }

    public function getFullName(): string
    {
        $fullName = $this->middle_name . " " . $this->first_name;
        if ($this->last_name) {
            $fullName .= " " . $this->last_name;
        }
        return $fullName;
    }

    public function getShortName(): string
    {
        $first_name = ucfirst(mb_substr($this->first_name, 0, 1)) . ".";

        $last_name = "";

        if ($this->last_name) {
            $last_name = ucfirst(mb_substr($this->last_name, 0, 1)) . ".";
        }

        $short_name = "{$this->middle_name} $first_name $last_name";

        return trim($short_name);
    }

    public function getMediumName(): string
    {
        return trim($this->first_name . " " . $this->middle_name);
    }
    public function fields(): array
    {
        $fields = parent::fields();
        $fields['full_name'] = function () {
            return $this->fullName;
        };
        $fields['short_name'] = function () {
            return $this->shortName;
        };
        $fields['medium_name'] = function () {
            return $this->mediumName;
        };
        return $fields;
    }
    /**
     * Gets query for [[CallLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCallLists()
    {
        return $this->hasMany(CallList::className(), ['caller_id' => 'caller_id']);
    }
    /**
     * Gets query for [[Phones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmails()
    {
        return $this->hasMany(UserProfileEmail::className(), ['user_profile_id' => 'id']);
    }

    /**
     * Gets query for [[Phones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhones()
    {
        return $this->hasMany(UserProfilePhone::className(), ['user_profile_id' => 'id']);
    }
    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
