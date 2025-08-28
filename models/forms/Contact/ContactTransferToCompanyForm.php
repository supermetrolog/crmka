<?php

declare(strict_types=1);

namespace app\models\forms\Contact;

use app\dto\Contact\TransferContactToCompanyDto;
use app\kernel\common\models\Form\Form;
use app\models\Company\Company;
use app\models\User;

class ContactTransferToCompanyForm extends Form
{
	public $company_id;
	public $consultant_id;
	public $disable_contact;
	public $is_main;

	public function rules(): array
	{
		return [
			[['company_id', 'disable_contact', 'consultant_id'], 'required'],
			[['company_id', 'consultant_id', 'is_main'], 'integer'],
			['company_id', 'exist', 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
			['consultant_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => ['consultant_id' => 'id']],
			[['disable_contact'], 'boolean']
		];
	}

	public function attributeLabels(): array
	{
		return [
			'company_id'      => 'ID компании',
			'consultant_id'   => 'ID консультанта',
			'archive_contact' => 'Архивировать контакт',
			'is_main'         => 'Основной контакт'
		];
	}

	public function getDto(): TransferContactToCompanyDto
	{
		return new TransferContactToCompanyDto([
			'company'         => Company::find()->byId((int)$this->company_id)->one(),
			'consultant'      => User::find()->byId((int)$this->consultant_id)->one(),
			'disable_contact' => $this->disable_contact,
			'is_main'         => $this->is_main
		]);
	}
}