<?php

declare(strict_types=1);

namespace app\resources\Company;

use app\kernel\web\http\resources\JsonResource;
use app\models\Company;

class CompanyBaseResource extends JsonResource
{
	private Company $resource;

	public function __construct(Company $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                   => $this->resource->id,
			'nameRu'               => $this->resource->nameRu,
			'nameEng'              => $this->resource->nameEng,
			'noName'               => $this->resource->noName,
			'full_name'            => $this->resource->getFullName(),
			'nameBrand'            => $this->resource->nameBrand,
			'rating'               => $this->resource->rating,
			'bik'                  => $this->resource->bik,
			'okved'                => $this->resource->okved,
			'okpo'                 => $this->resource->okpo,
			'ogrn'                 => $this->resource->ogrn,
			'inn'                  => $this->resource->inn,
			'kpp'                  => $this->resource->kpp,
			'checkingAccount'      => $this->resource->checkingAccount,
			'correspondentAccount' => $this->resource->correspondentAccount,
			'inTheBank'            => $this->resource->inTheBank,
			'signatoryName'        => $this->resource->signatoryName,
			'signatoryMiddleName'  => $this->resource->signatoryMiddleName,
			'signatoryLastName'    => $this->resource->signatoryLastName,
			'description'          => $this->resource->description,
			'formOfOrganization'   => $this->resource->formOfOrganization,
			'officeAdress'         => $this->resource->officeAdress,
			'legalAddress'         => $this->resource->legalAddress,
			'latitude'             => $this->resource->latitude,
			'longitude'            => $this->resource->longitude,
			'basis'                => $this->resource->basis,
			'documentNumber'       => $this->resource->documentNumber,
			'activityGroup'        => $this->resource->activityGroup,
			'activityProfile'      => $this->resource->activityProfile,
			'status'               => $this->resource->status,
			'active'               => $this->resource->active,
			'processed'            => $this->resource->processed,
			'passive_why'          => $this->resource->passive_why,
			'passive_why_comment'  => $this->resource->passive_why_comment,
			'consultant_id'        => $this->resource->consultant_id,
			'is_individual'        => $this->resource->is_individual,
			'individual_full_name' => $this->resource->individual_full_name,
			'created_at'           => $this->resource->created_at,
			'updated_at'           => $this->resource->updated_at
		];
	}
}