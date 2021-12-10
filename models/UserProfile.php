<?php

namespace app\models;

use Yii;
use app\exceptions\ValidationErrorHttpException;

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
 * @property CallList[] $callLists
 * @property User $user
 */
class UserProfile extends \yii\db\ActiveRecord
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
            'avatar' => "Avatar"

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
        if ($model->load($post_data, '')) {
            $model = $model->uploadFiles($uploadFileModel, $model);
            if ($model->save()) {
                return true;
            }
        }
        throw new ValidationErrorHttpException($model->getErrorSummary(false));
    }
    public function fields()
    {
        $fields = parent::fields();
        $fields['avatar'] = function ($fields) {
            if (!$fields['avatar']) {
                return "1.jpg";
            }
            return $fields['avatar'];
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
