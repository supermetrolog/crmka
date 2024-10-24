<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "productrange".
 *
 * @property int     $id
 * @property int     $company_id
 * @property string  $product
 *
 * @property Company $company
 */
class Productrange extends AR
{

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'productrange';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['company_id', 'product'], 'required'],
			[['company_id'], 'integer'],
			[['product'], 'string', 'max' => 255],
			[['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array
	{
		return [
			'id'         => 'ID',
			'company_id' => 'Company ID',
			'product'    => 'Product',
		];
	}

	/**
	 * Gets query for [[Company]].
	 *
	 * @return ActiveQuery
	 */
	public function getCompany(): ActiveQuery
	{
		return $this->hasOne(Company::class, ['id' => 'company_id']);
	}
}
