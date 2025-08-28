<?php

namespace app\dto\Company;

use app\models\User;
use yii\base\BaseObject;

class DeleteCompanyDto extends BaseObject
{
	public ?int    $passive_why;
	public ?string $comment;

	public ?User $initiator        = null;
	public bool  $disable_requests = true;
	public bool  $disable_contacts = true;
}