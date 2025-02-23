<?php

namespace app\dto\Company;

use yii\base\BaseObject;

class CompanyDto extends BaseObject
{
	public ?string $nameEng;
	public ?string $nameRu;
	public ?string $nameBrand;
	public ?int    $noName;
	public ?int    $formOfOrganization;
	public ?int    $companyGroup_id;
	public ?string $officeAdress;
	public int     $status;
	public int     $consultant_id;
	public ?string $legalAddress;
	public ?string $ogrn;
	public ?string $inn;
	public ?string $kpp;
	public ?string $checkingAccount;
	public ?string $correspondentAccount;
	public ?string $inTheBank;
	public ?string $bik;
	public ?string $okved;
	public ?string $okpo;
	public ?string $signatoryName;
	public ?string $signatoryMiddleName;
	public ?string $signatoryLastName;
	public ?string $basis;
	public ?string $documentNumber;
	public array   $activity_group_ids   = [];
	public array   $activity_profile_ids = [];
	public ?string $description;
	public ?int    $passive_why;
	public ?string $passive_why_comment;
	public ?int    $rating;
	public ?int    $processed;

	public array   $files = [];
	public ?int    $logo_id;
	public bool    $is_individual;
	public ?string $individual_full_name;
}