<?php

namespace app\models\forms\Utilities;

use app\dto\Utilities\TransferCompaniesToConsultantUtilitiesDto;
use app\kernel\common\models\Form\Form;

class UtilitiesTransferCompaniesToConsultantForm extends Form
{
	public $company_ids = [];

	public function rules(): array
	{
		return [
			['company_ids', 'required'],
			['company_ids', 'each', 'rule' => ['integer']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'company_ids' => 'ID компаний'
		];
	}

	public function getDto(): TransferCompaniesToConsultantUtilitiesDto
	{
		return new TransferCompaniesToConsultantUtilitiesDto([
			'companyIds' => $this->company_ids
		]);
	}
}