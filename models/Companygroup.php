<?php

namespace app\models;

use app\helpers\StringHelper;
use app\kernel\common\models\AR\AR;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "companygroup".
 *
 * @property int         $id
 * @property string      $nameEng
 * @property string      $nameRu
 * @property int|null    $formOfOrganization
 * @property string|null $description
 *
 * @property Company[]   $companies
 */
class Companygroup extends AR
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'companygroup';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['nameRu'], 'required'],
			[['description'], 'string'],
			[['formOfOrganization'], 'integer'],
			[['nameEng', 'nameRu'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array
	{
		return [
			'id'                 => 'ID',
			'nameEng'            => 'Name Eng',
			'nameRu'             => 'Name Ru',
			'description'        => 'Description',
			'formOfOrganization' => 'FormOfOrganization'
		];
	}

	public function getFullName(): string
	{
		$formOfOrganization = $this->formOfOrganization;
		$nameEng            = $this->nameEng;
		$nameRu             = $this->nameRu;

		$name = StringHelper::join(
			StringHelper::SYMBOL_SPACE,
			$nameRu,
			$formOfOrganization !== null ? Company::FORM_OF_ORGANIZATION_LIST[$formOfOrganization] : ""
		);

		return StringHelper::join(' - ', $name, $nameEng ?? "");
	}

	/**
	 * Gets query for [[Companies]].
	 *
	 * @return ActiveQuery
	 */
	public function getCompanies(): ActiveQuery
	{
		return $this->hasMany(Company::class, ['companyGroup_id' => 'id']);
	}
}
