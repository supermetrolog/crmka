<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "productrange".
 *
 * @property int $id
 * @property int $company_id
 * @property string $product
 *
 * @property Company $company
 */
class Productrange extends \yii\db\ActiveRecord
{
    public const MAIN_COLUMN = 'product';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'productrange';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'product'], 'required'],
            [['company_id'], 'integer'],
            [['product'], 'string', 'max' => 255],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'product' => 'Product',
        ];
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }
}
