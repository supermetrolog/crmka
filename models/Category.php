<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "category".
 *
 * @property int     $id
 * @property int     $company_id
 * @property int     $category
 *
 * @property Company $company
 */
class Category extends AR
{
	public const CATEGORY_CLIENT = 0;

	private const CATEGORY_NAMES = [
		self::CATEGORY_CLIENT => 'Клиент',
	];

	public static function getCategoryName(int $category): string
	{
		return self::CATEGORY_NAMES[$category] ?? '';
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'category';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['company_id', 'category'], 'required'],
			[['company_id', 'category'], 'integer'],
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
			'category'   => 'Category',
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
