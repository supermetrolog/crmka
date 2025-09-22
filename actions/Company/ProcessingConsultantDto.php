<?php

declare(strict_types=1);

namespace app\actions\Company;

use app\models\User\User;
use yii\base\BaseObject;


class ProcessingConsultantDto extends BaseObject
{
	public User $consultant;
	public int  $companiesCount;
}