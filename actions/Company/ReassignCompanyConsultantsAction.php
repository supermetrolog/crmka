<?php

declare(strict_types=1);

namespace app\actions\Company;

use app\helpers\ArrayHelper;
use app\helpers\StringHelper;
use app\kernel\common\actions\Action;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\ActiveQuery\CompanyQuery;
use app\models\ActiveQuery\RequestQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\Category;
use app\models\Company;
use app\models\Request;
use app\models\User;
use app\repositories\UserRepository;
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
		"karpushin",
		"matveev",
		"andrianov",
		"balashov",
		"otdelencev",
		"igorkaz",
		"mandryka"
	];

	// Company category can be changed if needed
	private const COMPANY_CATEGORIES = [
		Category::CATEGORY_CLIENT,
		Category::CATEGORY_OWNER,
	];

	private UserRepository               $userRepository;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		$id,
		$controller,
		UserRepository $userRepository,
		TransactionBeginnerInterface $transactionBeginner,
		array $config = []
	)
	{
		$this->userRepository      = $userRepository;
		$this->transactionBeginner = $transactionBeginner;

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

		$companiesCountToAssign = (int)$this->createNotAssignedCompaniesQuery()->count();

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

		$distributedCompanies = ArrayHelper::toDistributedValue(
			ArrayHelper::column($consultantsWhoNeedProcessing, 'companiesCount'),
			$companiesCountToAssign
		);

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
			$query                       = $this->createNotAssignedCompaniesQuery()->limit($neededCompaniesCount);
			$totalAssignedCompaniesCount = 0;

			foreach ($query->batch(50) as $companies) {
				$companiesIds                = ArrayHelper::column($companies, 'id');
				$totalAssignedCompaniesCount += ArrayHelper::length($companiesIds);

				Company::updateAll(['consultant_id' => $consultant->id], ['id' => $companiesIds]);
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

	// Create query for companies with active requests and filter by category
	private function createCompaniesQuery(): CompanyQuery
	{
		return Company::find()
		              ->innerJoinWith(['requests' => function (RequestQuery $query) {
			              return $query->andOnCondition([Request::field('status') => Request::STATUS_ACTIVE]);
		              }], false)
		              ->innerJoinWith(['categories' => function (ActiveQuery $query) {
			              return $query->andOnCondition([Category::field('category') => self::COMPANY_CATEGORIES]);
		              }], false);
	}

	private function createNotAssignedCompaniesQuery(): CompanyQuery
	{
		return $this->createCompaniesQuery()
		            ->innerJoinWith(['consultant' => function (UserQuery $query) {
			            return $query->andOnCondition(['!=', User::field('status'), User::STATUS_ACTIVE]);
		            }], false);
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