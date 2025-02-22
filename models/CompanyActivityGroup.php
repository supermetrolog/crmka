<?php

namespace app\models;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\CompanyQuery;

/**
 * @property int           $id
 * @property int           $company_id
 * @property int           $activity_group_id
 *
 * @property-read  Company $company
 */
class CompanyActivityGroup extends AR
{

	public static function tableName(): string
	{
		return 'company_activity_group';
	}

	public function rules(): array
	{
		return [
			[['company_id', 'activity_group_id'], 'required'],
			[['company_id', 'activity_group_id'], 'integer'],
			[['company_id'], 'exist', 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
		];
	}

	public function getCompany(): CompanyQuery
	{
		/** @var CompanyQuery */
		return $this->hasOne(Company::class, ['id' => 'company_id']);
	}

	public static function find(): AQ
	{
		return new AQ(self::class);
	}
}
