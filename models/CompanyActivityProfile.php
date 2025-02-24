<?php

namespace app\models;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;

/**
 * This is the model class for table "company_activity_profile".
 *
 * @property int           $id
 * @property int           $company_id
 * @property int           $activity_profile_id
 *
 * @property-read  Company $company
 */
class CompanyActivityProfile extends AR
{
	public static function tableName(): string
	{
		return 'company_activity_profile';
	}

	public function rules(): array
	{
		return [
			[['company_id', 'activity_profile_id'], 'required'],
			[['company_id', 'activity_profile_id'], 'integer'],
			[['company_id'], 'exist', 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
		];
	}

	public static function find(): AQ
	{
		return new AQ(self::class);
	}
}