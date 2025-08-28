<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\Company\Company;
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
	public const CATEGORY_CLIENT       = 0;
	public const CATEGORY_INTERMEDIARY = 1;
	public const CATEGORY_MONITORING   = 2;
	public const CATEGORY_OWNER        = 3;
	public const CATEGORY_APPRAISER    = 4;
	public const CATEGORY_CONTRACTOR   = 5;

	private const CATEGORY_NAMES = [
		self::CATEGORY_CLIENT       => 'Клиент',
		self::CATEGORY_INTERMEDIARY => 'Посредник',
		self::CATEGORY_MONITORING   => 'Мониторинг',
		self::CATEGORY_OWNER        => 'Собственник',
		self::CATEGORY_APPRAISER    => 'Оценщик',
		self::CATEGORY_CONTRACTOR   => 'Подрядчик',
	];

	public static function getCategoryName(int $category): string
	{
		return self::CATEGORY_NAMES[$category] ?? "Категория №$category";
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
