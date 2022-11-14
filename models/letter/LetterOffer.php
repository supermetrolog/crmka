<?php

namespace app\models\letter;

use Yii;

/**
 * This is the model class for table "letter_offer".
 *
 * @property int $id
 * @property int $letter_id [СВЯЗЬ] с отправленными письмами
 * @property int $original_id [СВЯЗЬ] с предложениями
 * @property int $object_id [СВЯЗЬ] с объектами
 * @property int $type_id Тип предложения (1,2,3)
 * @property string|null $class_name класс объекта
 * @property string|null $deal_type_name тип сделки
 * @property string|null $visual_id визуальный ID объекта
 * @property string|null $address адрес объекта
 * @property string|null $area площадь предложения
 * @property string|null $price цена предложения
 * @property string|null $image фото
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
            [['class_name', 'deal_type_name', 'visual_id', 'address', 'area', 'price', 'image'], 'string', 'max' => 255],
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
            'class_name' => 'Class Name',
            'deal_type_name' => 'Deal Type Name',
            'visual_id' => 'Visual ID',
            'address' => 'Address',
            'area' => 'Area',
            'price' => 'Price',
            'image' => 'Image',
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
