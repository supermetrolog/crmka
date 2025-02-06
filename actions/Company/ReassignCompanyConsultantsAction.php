<?php

declare(strict_types=1);

namespace app\actions\Company;

use app\helpers\ArrayHelper;
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
 *
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
	private const COMPANY_CATEGORY = Category::CATEGORY_CLIENT;

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
		$this->commentf('Company filters: category = "%s", Active Requests Count > 0', Category::getCategoryName(self::COMPANY_CATEGORY));
		$this->delimiter();

		$companiesCountToAssign = (int)$this->createCompaniesQuery()->count();

		if ($companiesCountToAssign > 0) {
			$this->infof('Companies count to assign: %d', $companiesCountToAssign);
			$this->delimiter();

			$this->assignCompaniesToConsultants($companiesCountToAssign);
		} else {
			$this->infof('No companies to assign, skipping...');
		}
	}

	/**
	 * @throws ErrorException
	 * @throws Throwable
	 */
	private function assignCompaniesToConsultants(int $companiesCountToAssign): void
	{
		try {
			[$processedConsultants, $processedConsultantsCompaniesCounts] = $this->getProcessedConsultants();
		} catch (ModelNotFoundException $th) {
			$this->warning('Assigning companies to consultants canceled: ' . $th->getMessage());

			return;
		}

		$this->delimiter();

		$distributedCompanies      = ArrayHelper::toDistributedValue(ArrayHelper::values($processedConsultantsCompaniesCounts), $companiesCountToAssign);
		$processedConsultantsCount = ArrayHelper::length($processedConsultants);

		$this->infof('Assigning companies to %d active consultants...', $processedConsultantsCount);

		$tx = $this->transactionBeginner->begin();

		try {
			for ($currentConsultantKey = 0; $currentConsultantKey < $processedConsultantsCount; $currentConsultantKey++) {
				$consultant                = $processedConsultants[$currentConsultantKey];
				$consultantCompaniesCount  = $processedConsultantsCompaniesCounts[$consultant->id];
				$distributedCompaniesCount = $distributedCompanies[$currentConsultantKey];

				if ($consultantCompaniesCount !== $distributedCompaniesCount) {
					$neededCompaniesCount = $distributedCompaniesCount - $consultantCompaniesCount;

					$this->assignCompaniesToConsultant(
						$consultant,
						(int)$neededCompaniesCount,
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
	 * @return array{User[], int[]}
	 * @throws ModelNotFoundException
	 * @throws ErrorException
	 */
	private function getProcessedConsultants(): array
	{
		$this->info('Current statistics:');

		$averageCompaniesCountPerConsultant = $this->getAverageCompaniesCountPerConsultant();

		$this->commentf('Average companies count per active consultant: %d', $averageCompaniesCountPerConsultant);
		$this->commentf('Finding consultants with less companies than average: %d', $averageCompaniesCountPerConsultant);

		$consultants             = [];
		$consultantsCompaniesMap = [];

		$totalCompaniesCount = 0;

		foreach (self::ACTIVE_CONSULTANT_USERNAMES as $activeConsultantUsername) {
			$consultant = $this->userRepository->getByUsername($activeConsultantUsername);

			if (is_null($consultant)) {
				$this->warning('No consultant found with username: ' . $activeConsultantUsername);

				$confirmed = $this->confirm('Continue assign companies to consultants?');

				if ($confirmed) {
					continue;
				}

				throw new ModelNotFoundException(sprintf('No consultant found with username "%s" ', $activeConsultantUsername));
			}

			$companiesCount = (int)$this->createCompaniesQueryByConsultantId($consultant->id)->count();

			if ($companiesCount < $averageCompaniesCountPerConsultant) {
				$consultants[]                            = $consultant;
				$consultantsCompaniesMap[$consultant->id] = $companiesCount;

				$processSymbol = '+';
				$color         = BaseConsole::FG_GREEN;
			} else {
				$processSymbol = '-';
				$color         = BaseConsole::FG_GREY;
			}

			$totalCompaniesCount += $companiesCount;

			$this->print(
				sprintf(
					'%s Companies already assigned to %s (%s): %d',
					$processSymbol,
					$consultant->userProfile->getMediumName(),
					$consultant->username,
					$companiesCount
				),
				$color
			);
		}

		$this->commentf('Total companies already assigned to consultants: %d', $totalCompaniesCount);


		return [
			$consultants,
			$consultantsCompaniesMap
		];
	}

	/**
	 * @throws Throwable
	 */
	private function assignCompaniesToConsultant(User $consultant, int $assignedCompaniesCount, int $currentCompaniesCount): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$query                       = $this->createCompaniesQuery()->limit($assignedCompaniesCount);
			$totalAssignedCompaniesCount = 0;

			foreach ($query->batch(50) as $companies) {
				$companiesIds                = ArrayHelper::column($companies, 'id');
				$totalAssignedCompaniesCount += ArrayHelper::length($companiesIds);

				Company::updateAll(['consultant_id' => $consultant->id], ['id' => $companiesIds]);
			}

			$tx->commit();

			$this->commentf(
				'Companies assigned to %s (%s): %d (+%d)',
				$consultant->userProfile->getMediumName(),
				$consultant->username,
				$totalAssignedCompaniesCount + $currentCompaniesCount,
				$totalAssignedCompaniesCount
			);
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	private function createBaseCompaniesQuery(): CompanyQuery
	{
		return Company::find()
		              ->innerJoinWith(['requests' => function (RequestQuery $query) {
			              return $query->andOnCondition([Request::field('status') => Request::STATUS_ACTIVE]);
		              }])
		              ->innerJoinWith(['categories' => function (ActiveQuery $query) {
			              return $query->andOnCondition([Category::field('category') => self::COMPANY_CATEGORY]);
		              }]);
	}

	private function createCompaniesQuery(): CompanyQuery
	{
		return $this->createBaseCompaniesQuery()
		            ->innerJoinWith(['consultant' => function (UserQuery $query) {
			            return $query->andOnCondition(['!=', User::field('status'), User::STATUS_ACTIVE]);
		            }]);
	}

	/**
	 * @throws ErrorException
	 */
	private function createCompaniesQueryByConsultantId(int $consultantId): CompanyQuery
	{
		return $this->createBaseCompaniesQuery()->andWhere([Company::field('consultant_id') => $consultantId]);
	}

	private function getAverageCompaniesCountPerConsultant(): int
	{
		$allCompaniesCount = (int)$this->createBaseCompaniesQuery()->count();

		return (int)ceil($allCompaniesCount / ArrayHelper::length(self::ACTIVE_CONSULTANT_USERNAMES));
	}

}