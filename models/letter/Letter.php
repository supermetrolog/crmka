<?php

namespace app\models\letter;

use app\models\User;
use Yii;

/**
 * This is the model class for table "letter".
 *
 * @property int $id
 * @property int $user_id [СВЯЗЬ] с таблицей юзеров
 * @property string|null $subject Тема письма
 * @property string|null $body Текст письма
 * @property string $created_at
 * @property int $status 1 - отправлено, 0 - ошибка
 *
 * @property User $user
 * @property LetterContact[] $letterContacts
 * @property LetterOffer[] $letterOffers
 */
class Letter extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'letter';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['body'], 'string'],
            [['created_at'], 'safe'],
            [['subject'], 'string', 'max' => 255],
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
            'subject' => 'Subject',
            'body' => 'Body',
            'created_at' => 'Created At',
            'status' => 'Status',
        ];
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

    /**
     * Gets query for [[LetterContacts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLetterContacts()
    {
        return $this->hasMany(LetterContact::className(), ['letter_id' => 'id']);
    }

    /**
     * Gets query for [[LetterOffers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLetterOffers()
    {
        return $this->hasMany(LetterOffer::className(), ['letter_id' => 'id']);
    }
}
