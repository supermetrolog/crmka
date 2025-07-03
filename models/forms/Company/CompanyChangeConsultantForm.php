<?php

declare(strict_types=1);

namespace app\models\forms\Company;

use app\dto\Company\ChangeCompanyConsultantDto;
use app\kernel\common\models\Form\Form;
use app\models\User;

class CompanyChangeConsultantForm extends Form
{
	public $consultant_id;
	public $change_requests_consultants = true;

	public function rules(): array
	{
		return [
			['consultant_id', 'required'],
			['consultant_id', 'integer'],
			['consultant_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => ['consultant_id' => 'id']],
			[['change_requests_consultants'], 'boolean']
		];
	}

	public function attributeLabels(): array
	{
		return [
			'consultant_id'               => 'ID консультанта',
			'change_requests_consultants' => 'Изменить консультанта в запросох'
		];
	}

	public function getDto(): ChangeCompanyConsultantDto
	{
		return new ChangeCompanyConsultantDto([
			'consultant'                  => User::find()->byId((int)$this->consultant_id)->one(),
			'change_requests_consultants' => $this->change_requests_consultants
		]);
	}
}