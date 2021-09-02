<?php

namespace app\models\miniModels;

use Yii;
use app\models\Contact;

/**
 * This is the model class for table "way_of_informing".
 *
 * @property int $id
 * @property int $contact_id
 * @property int $way
 *
 * @property Contact $contact
 */
class WayOfInforming extends \yii\db\ActiveRecord
{
    public const MAIN_COLUMN = 'way';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'way_of_informing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contact_id'], 'required'],
            [['contact_id', 'way'], 'integer'],
            [['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contact_id' => 'Contact ID',
            'way' => 'Way',
        ];
    }

    /**
     * Gets query for [[Contact]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(Contact::className(), ['id' => 'contact_id']);
    }
}
