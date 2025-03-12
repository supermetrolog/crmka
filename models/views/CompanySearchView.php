<?php

namespace app\models\views;

use app\models\Company;

class CompanySearchView extends Company
{
	public ?int $last_call_rel_id      = null;
	public int  $objects_count         = 0;
	public int  $requests_count        = 0;
	public int  $active_requests_count = 0;
	public int  $contacts_count        = 0;
	public int  $active_contacts_count = 0;
}
