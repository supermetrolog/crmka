<?php

namespace app\dto\Contact;

use app\models\Company\Company;
use app\models\User;
use yii\base\BaseObject;

class TransferContactToCompanyDto extends BaseObject
{
	public Company $company;
	public User    $consultant;
	public bool    $disable_contact = true;
	public ?int    $is_main         = null;
}