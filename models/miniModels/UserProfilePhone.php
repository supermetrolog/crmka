<?php

namespace app\models\miniModels;

use app\models\UserProfile;
use floor12\phone\PhoneFormatter;
use Yii;

/**
 * This is the model class for table "user_profile_phone".
 *
 * @property int $id
 * @property int $user_profile_id [СВЯЗЬ] с профилем юзера
 * @property string $phone номер телефона
 *
 * @property UserProfile $userProfile
 */
class UserProfilePhone extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_profile_phone';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_profile_id', 'phone'], 'required'],
            [['user_profile_id'], 'integer'],
            [['phone'], 'string', 'max' => 255],
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
            'phone' => 'Phone',
        ];
    }

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        preg_match_all('!\d+!', $this->phone, $numbers);
        $this->phone = implode('', $numbers[0]);
        return true;
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['phone'] = function ($fields) {
            return PhoneFormatter::format($fields['phone']);
        };
        return $fields;
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
