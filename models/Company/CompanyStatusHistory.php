<?php

namespace app\models\Company;

use app\enum\Company\CompanyStatusEnum;
use app\enum\Company\CompanyStatusReasonEnum;
use app\enum\Company\CompanyStatusSourceEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\CompanyQuery;
use app\models\ActiveQuery\CompanyStatusHistoryQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\User\User;

/**
 * @property int          $id
 * @property int          $company_id
 * @property int          $status
 * @property ?string      $reason
 * @property ?string      $comment
 * @property ?int         $changed_by_id
 * @property string       $changed_by_source
 * @property string       $created_at
 *
 * @property-read Company $company
 * @property-read User    $changedBy
 */
class CompanyStatusHistory extends AR
{
	public static function tableName(): string
	{
		return 'company_status_history';
	}

	public function rules(): array
	{
		return [
			[['company_id', 'status', 'changed_by_source'], 'required'],
			[['company_id', 'status', 'changed_by_id'], 'integer'],
			[['created_at'], 'safe'],
			['status', EnumValidator::class, 'enumClass' => CompanyStatusEnum::class],
			['reason', EnumValidator::class, 'enumClass' => CompanyStatusReasonEnum::class],
			['changed_by_source', EnumValidator::class, 'enumClass' => CompanyStatusSourceEnum::class],
			['comment', 'string', 'max' => 255],
			[['company_id'], 'exist', 'targetClass' => Company::class, 'targetAttribute' => 'id'],
		];
	}

	public static function find(): CompanyStatusHistoryQuery
	{
		return new CompanyStatusHistoryQuery(self::class);
	}

	public function getChangedBy(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'changed_by_id']);
	}

	public function getCompany(): CompanyQuery
	{
		/** @var CompanyQuery */
		return $this->hasOne(Company::class, ['id' => 'company_id']);
	}
}