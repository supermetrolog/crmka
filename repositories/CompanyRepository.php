<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\repository\AbstractRepository;
use app\models\Company;
use app\models\Objects;
use app\models\views\CompanySearchView;
use yii\base\ErrorException;

class CompanyRepository extends AbstractRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): Company
	{
		return Company::find()->byId($id)->oneOrThrow();
	}

	public function findOne(int $id): ?Company
	{
		return Company::find()->byId($id)->one();
	}

	/**
	 * @return Company[]
	 */
	public function findAll(): array
	{
		return Company::find()->all();
	}

	/**
	 * @return string[]
	 * @throws ErrorException
	 */
	public function getBankNameUniqueAll(): array
	{
		return Company::find()->distinct()->select(Company::field('inTheBank'))->andWhereNotNull('inTheBank')->column();
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws ErrorException
	 */
	public function findOneOrThrowWithRelations(int $id): Company
	{
		return CompanySearchView::find()
		                        ->select([
			                        Company::field('*'),
			                        'objects_count'         => 'COUNT(DISTINCT ' . Objects::field('id') . ' )',
			                        'requests_count'        => 'COUNT(DISTINCT request.id)',
			                        'active_requests_count' => 'COUNT(DISTINCT CASE WHEN request.status = 1 THEN request.id ELSE NULL END)',
			                        'contacts_count'        => 'COUNT(DISTINCT contact.id)',
			                        'active_contacts_count' => 'COUNT(DISTINCT CASE WHEN contact.status = 1 THEN contact.id ELSE NULL END)',
		                        ])
		                        ->byId($id)
		                        ->joinWith(['requests', 'contacts', 'objects', 'chatMember cm'])
		                        ->leftJoinLastCallRelation()
		                        ->with(['productRanges',
		                                'categories',
		                                'companyGroup',
		                                'deals',
		                                'files',
		                                'dealsRequestEmpty.consultant.userProfile',
		                                'dealsRequestEmpty.offer.generalOffersMix',
		                                'dealsRequestEmpty.competitor',
		                                'consultant.userProfile',
		                                'lastCall.user.userProfile',
		                                'contacts' => function ($query) {
			                                $query->with(['phones', 'emails', 'contactComments', 'websites']);
		                                }])
		                        ->oneOrThrow();
	}
}