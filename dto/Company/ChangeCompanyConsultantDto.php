<?php

namespace app\dto\Company;

use app\models\User\User;
use yii\base\BaseObject;

class ChangeCompanyConsultantDto extends BaseObject
{
	public User $consultant;
	public bool $change_requests_consultants = true;
}