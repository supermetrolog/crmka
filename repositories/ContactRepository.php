<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\ActiveQuery\CallQuery;
use app\models\Contact;

class ContactRepository
{
	public function findAllByCompanyId(int $companyId): array
	{
		return Contact::find()
		              ->with([
			              'emails', 'phones', 'websites', 'wayOfInformings', 'consultant.userProfile', 'contactComments.author.userProfile',
			              'calls' => function (CallQuery $query) {
				              $query->addOrderBy(['created_at' => SORT_DESC]);
			              }
		              ])
		              ->byCompanyId($companyId)
		              ->all();
	}
}