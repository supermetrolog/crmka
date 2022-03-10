<?php

namespace app\models\miniModels;

use app\models\UserProfile;
use Yii;

/**
 * This is the model class for table "user_profile_email".
 *
 * @property int $id
 * @property int $user_profile_id [СВЯЗЬ] с профилем юзера
 * @property string $email email
 *
 * @property UserProfile $userProfile
 */
class UserProfileEmail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_profile_email';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_profile_id', 'email'], 'required'],
            [['user_profile_id'], 'integer'],
            [['email'], 'string', 'max' => 255],
            [['user_profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserProfile::className(), 'targetAttribute' => ['user_profile_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_profile_id' => 'User Profile ID',
            'email' => 'Email',
        ];
    }

    /**
     * Gets query for [[UserProfile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['id' => 'user_profile_id']);
    }
}
