<?php

namespace app\models\letter;

use Yii;

/**
 * This is the model class for table "letter_way".
 *
 * @property int $id
 * @property int $letter_id [СВЯЗЬ] с отправленными письмами
 * @property int $way Каким способом отправлено письмо
 *
 * @property Letter $letter
 */
class LetterWay extends \yii\db\ActiveRecord
{
    public const WAY_EMAIL = 0;
    public const WAY_SMS = 1;
    public const WAY_WHATSAPP = 2;
    public const WAY_TELEGRAM = 3;
    public const WAY_VIBER = 4;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'letter_way';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['letter_id', 'way'], 'required'],
            ['way', 'in', 'range' => [self::WAY_EMAIL, self::WAY_SMS, self::WAY_WHATSAPP, self::WAY_TELEGRAM, self::WAY_VIBER]],
            [['letter_id', 'way'], 'integer'],
            [['letter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Letter::className(), 'targetAttribute' => ['letter_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'letter_id' => 'Letter ID',
            'way' => 'Way',
        ];
    }

    /**
     * Gets query for [[Letter]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLetter()
    {
        return $this->hasOne(Letter::className(), ['id' => 'letter_id']);
    }
}
