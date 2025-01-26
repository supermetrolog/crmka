<?php

declare(strict_types=1);

namespace app\actions\Import\ToOld;

use app\helpers\ArrayHelper;
use app\helpers\DateTimeHelper;
use app\kernel\common\actions\Action;
use app\kernel\common\models\AQ\AQ;
use app\models\ActiveQuery\ContactQuery;
use app\models\Company;
use app\models\miniModels\Email;
use app\models\miniModels\Phone;
use app\models\miniModels\Website;
use app\models\oldDb\Company as OldCompany;
use yii\db\Exception;
use yii\helpers\Json;

class Companies extends Action
{
	/**
	 * @throws Exception
	 * @throws \Exception
	 */
	public function run(): void
	{
		$this->info('Run import companies to old database');

		$query = Company::find()->with([
			'consultant',
			'generalContact',
			'generalContact.websites' => fn(AQ $query) => $query->orderBy(['id' => SORT_ASC]),
			'generalContact.emails'   => fn(AQ $query) => $query->orderBy(['id' => SORT_ASC]),
			'generalContact.phones'   => fn(AQ $query) => $query->orderBy(['id' => SORT_ASC]),
		]);

		$this->infof('Companies count: %d', $query->count());

		/** @var Company $company */
		foreach ($query->each() as $company) {
			$attributes = $this->mapAttributes($company);

			OldCompany::upsert($attributes, $attributes);
		}
	}

	/**
	 * @throws \Exception
	 */
	private function mapAttributes(Company $company): array
	{
		return [
			'id'                           => $company->id,
			'title'                        => $company->nameRu,
			'title_eng'                    => $company->nameEng,
			//			'title_old' => '',
			'noname'                       => $company->noName,
			//			'company_type'                 => 0,
			'company_activity'             => 0,
			'company_group'                => Json::encode($this->mapCategories($company)),
			'company_group_id'             => $company->companyGroup_id,
			'address'                      => $company->officeAdress,
			'latitude'                     => $company->latitude,
			'longitude'                    => $company->longitude,
			//			'address_yandex'               => '',
			//			'address_google'               => '',
			'site_url'                     => $company->generalContact->getFirstWebsite(),
			'agent_id'                     => $company->consultant->user_id_old,
			'contact_id'                   => $company->generalContact->id, // TODO: точно ли general?
			'company_service'              => '', // TODO:
			'company_service_name'         => '', // TODO:
			'description'                  => $company->description,
			//			'comment'                      => '',
			'rating'                       => $company->rating,
			//			'order_row'                    => '',
			'publ_time'                    => DateTimeHelper::make($company->created_at)->getTimestamp(),
			'last_update'                  => DateTimeHelper::make($company->updated_at)->getTimestamp(),
			'deleted'                      => $company->isDeleted(),
			//			'activity'                     => '',
			'company_law_type'             => $this->mapFormOfOrganization($company),
			//			'ready_for_safe'               => '',
			//			'ready_for_buy'                => '',
			//			'price_now'                    => '',
			//			'call_info_status'             => '',
			'sites_urls'                   => Json::encode(ArrayHelper::map($company->generalContact->websites, fn(Website $website) => $website->website)),
			'phones'                       => Json::encode(ArrayHelper::map($company->generalContact->phones, fn(Phone $phone) => $phone->phone)),
			'emails'                       => Json::encode(ArrayHelper::map($company->generalContact->emails, fn(Email $email) => $email->email)),
			//			'good_relationship'            => '',
			//			'status'                       => '', // TODO: проверить на проде
			//			'status_reason'                => '',
			//			'status_description'           => '',
			//			'processed'                    => '',
			'company_service_profile'      => '', // TODO:
			'company_service_nomenclature' => '', // TODO:
			//			'empty_line'                   => '',
			'law_address'                  => $company->legalAddress,
			'law_ogrn'                     => $company->ogrn,
			'law_inn'                      => $company->inn,
			'law_kpp'                      => $company->kpp,
			'law_account_checking'         => $company->checkingAccount,
			'law_account_correspondent'    => $company->correspondentAccount,
			'law_bank'                     => $company->inTheBank,
			'law_bik'                      => $company->bik,
			'law_code_okved'               => $company->okved,
			'law_code_okpo'                => $company->okpo,
			'law_first_name'               => $company->signatoryName,
			'law_second_name'              => $company->signatoryMiddleName,
			'law_father_name'              => $company->signatoryLastName,
			'law_action'                   => $company->basis,
			'law_document_num'             => $company->documentNumber,
			//			'title_empty_old'              => '',
			'phone'                        => $company->generalContact->getFirstPhone(),
			'email'                        => $company->generalContact->getFirstEmail(),
			//			'documents_old'                => '',
			//			'documents'                    => '',
		];
	}

	private function mapFormOfOrganization(Company $company): int
	{
		$map = [
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
			4 => 5,
			5 => 6,
			6 => 7,
		];

		return $map[$company->formOfOrganization];
	}

	private function mapCategories(Company $company): array
	{
		$map = [
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 5,
			4 => 6,
			5 => 7,
		];

		$categories = [];

		foreach ($company->categories as $category) {
			$categories[] = $map[$category->category];
		}

		return $categories;
	}
}