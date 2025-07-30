<?php

namespace app\mappers\Contact;

use app\dto\Contact\CreateContactDto;
use app\helpers\ArrayHelper;
use app\mappers\AbstractDtoMapper;
use app\models\Contact;
use app\models\miniModels\Email;
use app\models\miniModels\WayOfInforming;
use app\models\miniModels\Website;
use Exception;

class CreateContactDtoMapper extends AbstractDtoMapper
{
	/**
	 * @throws Exception
	 */
	public function fromRecord(Contact $contact): CreateContactDto
	{
		return new CreateContactDto([
			'company_id'          => $contact->company_id,
			'consultant_id'       => $contact->consultant_id,
			'isMain'              => $contact->isMain,
			'first_name'          => $contact->first_name,
			'middle_name'         => $contact->middle_name,
			'last_name'           => $contact->last_name,
			'position'            => $contact->position,
			'position_unknown'    => $contact->position_unknown,
			'faceToFaceMeeting'   => $contact->faceToFaceMeeting,
			'warning'             => $contact->warning,
			'good'                => $contact->good,
			'passive_why'         => $contact->passive_why,
			'passive_why_comment' => $contact->passive_why_comment,
			'warning_why_comment' => $contact->warning_why_comment,
			'type'                => $contact->type,
			'status'              => $contact->status,
			'emails'              => ArrayHelper::map($contact->emails, static fn(Email $e) => ['email' => $e->email]),
			'websites'            => ArrayHelper::map($contact->websites, static fn(Website $w) => ['website' => $w->website]),
			'wayOfInformings'     => ArrayHelper::map($contact->wayOfInformings, static fn(WayOfInforming $w) => ['way' => $w->way]),
		]);
	}
}