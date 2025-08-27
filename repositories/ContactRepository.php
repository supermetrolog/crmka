<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
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
			              },
			              'lettersContacts.answers.markedBy.userProfile', 'lettersContacts.letter.user.userProfile', 'lettersContacts.events'
		              ])
		              ->byCompanyId($companyId)
		              ->all();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): Contact
	{
		return Contact::find()->byId($id)->oneOrThrow();
	}
}