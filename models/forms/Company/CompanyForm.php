<?php

declare(strict_types=1);

namespace app\models\forms\Company;

use app\dto\Company\CompanyDto;
use app\kernel\common\models\Form\Form;
use app\models\Companygroup;
use app\models\Media;
use app\models\User;

class CompanyForm extends Form
{
	public $nameEng;
	public $nameRu;
	public $nameBrand;
	public $noName;
	public $formOfOrganization;
	public $companyGroup_id;
	public $officeAdress;
	public $status;
	public $consultant_id;
	public $legalAddress;
	public $ogrn;
	public $inn;
	public $kpp;
	public $checkingAccount;
	public $correspondentAccount;
	public $inTheBank;
	public $bik;
	public $okved;
	public $okpo;
	public $signatoryName;
	public $signatoryMiddleName;
	public $signatoryLastName;
	public $basis;
	public $documentNumber;
	public $activityGroup;
	public $activityProfile;
	public $description;
	public $passive_why;
	public $passive_why_comment;
	public $rating;
	public $processed;
	public $broker_id;

	public $files                = [];
	public $logo_id;
	public $is_individual        = false;
	public $individual_full_name = null;

	public function rules(): array
	{
		return [
			[['noName', 'companyGroup_id', 'status', 'consultant_id', 'broker_id', 'activityGroup', 'activityProfile', 'formOfOrganization', 'processed', 'passive_why', 'rating', 'logo_id'], 'integer'],
			[['consultant_id', 'activityGroup', 'activityProfile'], 'required'],
			[['nameRu', 'nameEng', 'noName'], 'validateCompanyName'],
			[['description'], 'string'],
			[['is_individual'], 'boolean'],
			[['nameBrand', 'nameEng', 'nameRu', 'officeAdress', 'legalAddress', 'ogrn', 'inn', 'kpp', 'checkingAccount', 'correspondentAccount', 'inTheBank', 'bik', 'okved', 'okpo', 'signatoryName', 'signatoryMiddleName', 'signatoryLastName', 'basis', 'documentNumber', 'passive_why_comment', 'individual_full_name'], 'string', 'max' => 255],
			[['broker_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['broker_id' => 'id']],
			[['companyGroup_id'], 'exist', 'skipOnError' => true, 'targetClass' => Companygroup::class, 'targetAttribute' => ['companyGroup_id' => 'id']],
			[['consultant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['consultant_id' => 'id']],
			[['files'], 'safe'],
			[['logo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Media::class, 'targetAttribute' => ['logo_id' => 'id']]
		];
	}


	public function validateCompanyName(string $attribute): void
	{
		if (!$this->hasErrors()) {
			if ((int)$this->noName === 1) {
				return;
			}

			if (empty($this->nameRu) && empty($this->nameEng)) {
				$this->addError($attribute, 'Компания должна или иметь название на одном из языков или иметь отметку "Без названия');
			}
		}
	}

	public function getDto(): CompanyDto
	{
		return new CompanyDto([
			'nameEng'              => $this->nameEng,
			'nameRu'               => $this->nameRu,
			'nameBrand'            => $this->nameBrand,
			'noName'               => $this->noName,
			'formOfOrganization'   => $this->formOfOrganization,
			'companyGroup_id'      => $this->companyGroup_id,
			'officeAdress'         => $this->officeAdress,
			'status'               => $this->status,
			'consultant_id'        => $this->consultant_id,
			'legalAddress'         => $this->legalAddress,
			'ogrn'                 => $this->ogrn,
			'inn'                  => $this->inn,
			'kpp'                  => $this->kpp,
			'checkingAccount'      => $this->checkingAccount,
			'correspondentAccount' => $this->correspondentAccount,
			'inTheBank'            => $this->inTheBank,
			'bik'                  => $this->bik,
			'okved'                => $this->okved,
			'okpo'                 => $this->okpo,
			'signatoryName'        => $this->signatoryName,
			'signatoryMiddleName'  => $this->signatoryMiddleName,
			'signatoryLastName'    => $this->signatoryLastName,
			'basis'                => $this->basis,
			'documentNumber'       => $this->documentNumber,
			'activityGroup'        => $this->activityGroup,
			'activityProfile'      => $this->activityProfile,
			'description'          => $this->description,
			'passive_why'          => $this->passive_why,
			'passive_why_comment'  => $this->passive_why_comment,
			'rating'               => $this->rating,
			'processed'            => $this->processed,
			'is_individual'        => $this->is_individual,
			'individual_full_name' => $this->individual_full_name,

			'files'   => $this->files,
			'logo_id' => $this->logo_id
		]);
	}
}