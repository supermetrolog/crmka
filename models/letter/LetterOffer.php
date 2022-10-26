<?php

namespace app\models\letter;

use app\models\oldDb\OfferMix;
use Yii;

/**
 * This is the model class for table "letter_offer".
 *
 * @property int $id
 * @property int $letter_id [СВЯЗЬ] с отправленными письмами
 * @property int $original_id [СВЯЗЬ] с предложениями
 * @property int $object_id [СВЯЗЬ] с объектами
 * @property int $type_id Тип предложения (1,2,3)
 *
 * @property Letter $letter
 */
class LetterOffer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'letter_offer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['letter_id', 'original_id', 'object_id', 'type_id'], 'required'],
            [['letter_id', 'original_id', 'object_id', 'type_id'], 'integer'],
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
            'original_id' => 'Original ID',
            'object_id' => 'Object ID',
            'type_id' => 'Type ID',
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

    /**
     * Gets query for [[Offers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOffer()
    {
        return $this->hasOne(OfferMix::className(), ['original_id' => 'original_id', 'object_id' => 'object_id', 'type_id' => 'type_id']);
    }
}
