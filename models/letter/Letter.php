<?php

namespace app\models\letter;

use app\models\User;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "letter".
 *
 * @property int $id
 * @property int $user_id [СВЯЗЬ] с таблицей юзеров
 * @property string|null $subject Тема письма
 * @property string|null $body Текст письма
 * @property string $created_at
 * @property int $status 1 - отправлено, 0 - ошибка
 * @property int $type Отправлено из таймлайна или другим способом
 * @property int $shipping_method Отправлено из таймлайна или другим способом
 *
 * @property User $user
 * @property LetterContact[] $letterContacts
 * @property LetterOffer[] $letterOffers
 * @property LetterWay[] $letterWays
 */
class Letter extends \yii\db\ActiveRecord
{
    const TYPE_FROM_TIMELINE = 1;
    const STATUS_ERROR = 0;
    const STATUS_SUCCESS = 1;

    const SHIPPING_FROM_SYSTEM_METHOD = 1;
    const SHIPPING_OTHER_METHOD = 0;
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
            [['user_id', 'type', 'shipping_method'], 'required'],
            [['user_id', 'status', 'type', 'shipping_method'], 'integer'],
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
            'type' => 'Type',
            'shipping_method' => 'Shippin Method',
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
     * Gets query for [[LetterContacts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLetterEmails()
    {
        return $this->hasMany(LetterContact::className(), ['letter_id' => 'id'])->where(['is not', 'email_id', new Expression("null")]);
    }

    /**
     * Gets query for [[LetterContacts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLetterPhones()
    {
        return $this->hasMany(LetterContact::className(), ['letter_id' => 'id'])->where(['is not', 'phone_id', new Expression("null")]);
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

    /**
     * Gets query for [[LetterWays]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLetterWays()
    {
        return $this->hasMany(LetterWay::className(), ['letter_id' => 'id']);
    }
}
