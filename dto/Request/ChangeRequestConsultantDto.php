<?php

declare(strict_types=1);

namespace app\dto\Request;

use app\models\User;
use yii\base\BaseObject;

class ChangeRequestConsultantDto extends BaseObject
{
	public User $consultant;
}