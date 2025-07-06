<?php

declare(strict_types=1);

namespace app\actions\Company;

use app\dto\Request\ChangeRequestConsultantDto;
use app\helpers\ArrayHelper;
use app\helpers\StringHelper;
use app\kernel\common\actions\Action;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ActiveQuery\CompanyQuery;
use app\models\ActiveQuery\RequestQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\Category;
use app\models\Company;
use app\models\Request;
use app\models\User;
use app\repositories\UserRepository;
use app\usecases\Request\RequestService;
use Throwable;
use yii\base\ErrorException;
use yii\db\ActiveQuery;
use yii\helpers\BaseConsole;

/**
 * This action is used to assign companies with selected category and active requests and with archived consultants to active consultants.
 */
class ReassignCompanyConsultantsAction extends Action
{
	// Active consultants usernames must be sorted by companies count (asc)
	private const ACTIVE_CONSULTANT_USERNAMES = [
		"a.brusensky@mail.ru"
	];

	// Company category can be changed if needed
	private const COMPANY_CATEGORIES = [
		Category::CATEGORY_CLIENT,
		Category::CATEGORY_OWNER,
	];

	private const REASSIGN_STRATEGY_INACTIVE_CONSULTANTS = 'inactive';
	private const REASSIGN_STRATEGY_SYSTEM_CONSULTANT    = 'system';

	private const CURRENT_REASSIGN_STRATEGY = self::REASSIGN_STRATEGY_SYSTEM_CONSULTANT;

	private const MAX_COMPANIES_TO_ASSIGN = 300;
	private const CREATED_AFTER_DATE      = '2019-01-01';

	private UserRepository               $userRepository;
	private TransactionBeginnerInterface $transactionBeginner;
	private RequestService               $requestService;

	public function __construct(
		$id,
		$controller,
		UserRepository $userRepository,
		TransactionBeginnerInterface $transactionBeginner,
		RequestService $requestService,
		array $config = []
	)
	{
		$this->userRepository      = $userRepository;
		$this->transactionBeginner = $transactionBeginner;
		$this->requestService      = $requestService;

		parent::__construct($id, $controller, $config);
	}


	/**
	 * @throws ErrorException
	 * @throws Throwable
	 */
	public function run(): void
	{
		$this->info('Start reassigning companies to consultants...');
		$this->printCompanyFilters();
		$this->delimiter();

		$companiesCount = (int)$this->createNotAssignedCompaniesQuery()->count();

		$companiesCountToAssign = min($companiesCount, self::MAX_COMPANIES_TO_ASSIGN);


		if ($companiesCountToAssign > 0) {
			$this->infof('Not assigned companies count with selected filters: %d', $companiesCountToAssign);
			$this->delimiter();

			$this->distributeCompaniesToActiveConsultants($companiesCountToAssign, self::ACTIVE_CONSULTANT_USERNAMES);
		} else {
			$this->infof('No companies to assign, skipping...');
		}
	}

	private function printCompanyFilters(): void
	{
		$this->commentf(
			'Company filters: category = "%s", Active Requests Count > 0',
			StringHelper::join(
				StringHelper::SPACED_COMMA,
				...ArrayHelper::map(
				self::COMPANY_CATEGORIES,
				static fn(int $category) => Category::getCategoryName($category)))
		);
	}

	/**
	 * @param string[] $activeConsultantUsernames
	 *
	 * @throws ErrorException
	 * @throws Throwable
	 */
	private function distributeCompaniesToActiveConsultants(int $companiesCountToAssign, array $activeConsultantUsernames): void
	{
		try {
			$consultantsWhoNeedProcessing = $this->findConsultantsWhoNeedProcessing($activeConsultantUsernames);
		} catch (ModelNotFoundException $th) {
			$this->warning('Assigning companies to consultants canceled: ' . $th->getMessage());

			return;
		}

		$this->delimiter();

		$distributedCompanies = ArrayHelper::column($consultantsWhoNeedProcessing, 'companiesCount');
		ArrayHelper::distributeValue($distributedCompanies, $companiesCountToAssign);

		$consultantsWhoNeedProcessingCount = ArrayHelper::length($consultantsWhoNeedProcessing);

		$this->infof('Assigning %d companies to %d active consultants...', $companiesCountToAssign, $consultantsWhoNeedProcessingCount);

		$tx = $this->transactionBeginner->begin();

		try {
			for ($currentConsultantKey = 0; $currentConsultantKey < $consultantsWhoNeedProcessingCount; $currentConsultantKey++) {
				$consultant               = $consultantsWhoNeedProcessing[$currentConsultantKey]->consultant;
				$consultantCompaniesCount = $consultantsWhoNeedProcessing[$currentConsultantKey]->companiesCount;

				$distributedCompaniesCount = $distributedCompanies[$currentConsultantKey];

				if ($consultantCompaniesCount !== $distributedCompaniesCount) {
					$neededCompaniesCount = $distributedCompaniesCount - $consultantCompaniesCount;

					$this->assignCompaniesToConsultant(
						$consultant,
						$neededCompaniesCount,
						$consultantCompaniesCount
					);
				}
			}

			$tx->commit();

			$this->delimiter();
			$this->infof('Done!');
		} catch (Throwable $th) {
			$tx->rollBack();

			$this->error($th->getMessage());

			throw $th;
		}
	}

	/**
	 * @param string[] $consultantUsernames
	 *
	 * @return ProcessingConsultantDto[]
	 * @throws ModelNotFoundException
	 * @throws ErrorException
	 */
	private function findConsultantsWhoNeedProcessing(array $consultantUsernames): array
	{
		$this->info('Current statistics:');

		$averageCompaniesCountPerConsultant = $this->getAverageCompaniesCountPerConsultant();

		$this->commentf('Average companies count per active consultant: %d', $averageCompaniesCountPerConsultant);
		$this->delimiter();
		$this->info('Finding consultants with less companies than average...');

		$consultantsWithCompaniesCount = [];

		foreach ($consultantUsernames as $username) {
			$consultant = $this->userRepository->getByUsername($username);

			if (is_null($consultant)) {
				$this->warning('No consultant found with username: ' . $username);

				$confirmed = $this->confirm('Continue assign companies to consultants?');

				if ($confirmed) {
					continue;
				}

				throw new ModelNotFoundException(sprintf('No consultant found with username "%s" ', $username));
			}

			$companiesCount = $this->getConsultantCompaniesCount($consultant);

			if ($companiesCount < $averageCompaniesCountPerConsultant) {
				$consultantsWithCompaniesCount[] = new ProcessingConsultantDto([
					'consultant'     => $consultant,
					'companiesCount' => $companiesCount
				]);

				$processSymbol = '+';
				$color         = BaseConsole::FG_GREEN;
			} else {
				$processSymbol = '-';
				$color         = BaseConsole::FG_GREY;
			}

			$this->print(
				sprintf(
					'%s Now %s (%s) has %d companies with selected filters',
					$processSymbol,
					$consultant->userProfile->getMediumName(),
					$consultant->username,
					$companiesCount
				),
				$color
			);
		}

		usort($consultantsWithCompaniesCount, static fn(ProcessingConsultantDto $a, ProcessingConsultantDto $b) => $a->companiesCount - $b->companiesCount);

		return $consultantsWithCompaniesCount;
	}

	/**
	 * @throws Throwable
	 */
	private function assignCompaniesToConsultant(User $consultant, int $neededCompaniesCount, int $currentCompaniesCount): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$query                       = $this->createNotAssignedCompaniesQuery();
			$totalAssignedCompaniesCount = 0;

			$limit = min(50, $neededCompaniesCount);

			while ($totalAssignedCompaniesCount < $neededCompaniesCount) {
				$companies = $query->limit($limit)->all();

				$totalAssignedCompaniesCount += $limit;
				$limit                       = min($limit, $neededCompaniesCount - $totalAssignedCompaniesCount);

				foreach ($companies as $company) {
					$this->changeCompanyConsultant($company, $consultant);
				}
			}

			$tx->commit();

			$this->commentf(
				'%d companies assigned to %s (%s), final companies count: %d',
				$totalAssignedCompaniesCount,
				$consultant->userProfile->getMediumName(),
				$consultant->username,
				$totalAssignedCompaniesCount + $currentCompaniesCount
			);
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function changeCompanyConsultant(Company $company, User $consultant): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$company->updateAttributes(['consultant_id' => $consultant->id]);

			$requests = $company->activeRequests;

			foreach ($requests as $request) {
				$this->requestService->changeConsultant($request, new ChangeRequestConsultantDto(['consultant' => $consultant]));
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	// Create query for companies with active requests and filter by category

	/**
	 * @throws ErrorException
	 */
	private function createCompaniesQuery(): CompanyQuery
	{
		return Company::find()
		              ->active()
		              ->innerJoinWith(['requests' => function (RequestQuery $query) {
			              return $query->andOnCondition([Request::field('status') => Request::STATUS_ACTIVE]);
		              }], false)
		              ->innerJoinWith(['categories' => function (ActiveQuery $query) {
			              return $query->andOnCondition([Category::field('category') => self::COMPANY_CATEGORIES]);
		              }], false)
		              ->createdAfter(self::CREATED_AFTER_DATE)
		              ->groupBy(Company::field('id'));
	}

	/**
	 * @throws ErrorException
	 */
	private function createNotAssignedCompaniesQuery(): CompanyQuery
	{
		switch (self::CURRENT_REASSIGN_STRATEGY) {
			case self::REASSIGN_STRATEGY_INACTIVE_CONSULTANTS:
			{
				return $this->createCompaniesQuery()
				            ->innerJoinWith(['consultant' => function (UserQuery $query) {
					            return $query->andOnCondition(['!=', User::field('status'), User::STATUS_ACTIVE]);
				            }], false);
			}
			case self::REASSIGN_STRATEGY_SYSTEM_CONSULTANT:
			{
				return $this->createCompaniesQuery()
				            ->innerJoinWith(['consultant' => function (UserQuery $query) {
					            return $query->andOnCondition([User::field('role') => User::ROLE_SYSTEM]);
				            }], false);
			}
		}

		throw new ErrorException('Unknown reassign strategy');
	}

	private function getAverageCompaniesCountPerConsultant(): int
	{
		$allCompaniesCount = (int)$this->createCompaniesQuery()->count();

		return (int)ceil($allCompaniesCount / ArrayHelper::length(self::ACTIVE_CONSULTANT_USERNAMES));
	}

	/**
	 * @throws ErrorException
	 */
	private function getConsultantCompaniesCount(User $consultant): int
	{
		return (int)$this->createCompaniesQuery()
		                 ->andWhere([Company::field('consultant_id') => $consultant->id])
		                 ->count();
	}

}