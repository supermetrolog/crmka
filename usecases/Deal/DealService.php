<?php

declare(strict_types=1);

namespace app\usecases\Deal;

use app\components\EventManager;
use app\dto\Deal\CreateDealDto;
use app\dto\Deal\CreateRequestDealDto;
use app\dto\Deal\UpdateDealDto;
use app\enum\Deal\DealStatusEnum;
use app\events\Deal\CreateRequestDealEvent;
use app\exceptions\services\RequestDealAlreadyExistsException;
use app\helpers\DateTimeHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Deal;
use app\models\Request;
use Throwable;
use yii\db\StaleObjectException;

class DealService
{
	protected TransactionBeginnerInterface $transactionBeginner;
	protected EventManager                 $eventManager;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		EventManager $eventManager
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->eventManager        = $eventManager;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function createForRequest(Request $request, CreateRequestDealDto $dto): Deal
	{
		// TODO: Учитывать company_id возможно (было в старой логике)

		if ($request->hasDeal()) {
			throw new RequestDealAlreadyExistsException("Request already has deal");
		}

		$tx = $this->transactionBeginner->begin();

		try {
			$deal = $this->create(
				new CreateDealDto([
					'request' => $request,

					'consultant'         => $dto->consultant,
					'object'             => $dto->object,
					'complex'            => $dto->complex,
					'company'            => $dto->company,
					'type_id'            => $dto->type_id,
					'original_id'        => $dto->original_id,
					'visual_id'          => $dto->visual_id,
					'clientLegalEntity'  => $dto->clientLegalEntity,
					'description'        => $dto->description,
					'name'               => $dto->name,
					'area'               => $dto->area,
					'floorPrice'         => $dto->floorPrice,
					'competitor'         => $dto->competitor,
					'contractTerm'       => $dto->contractTerm,
					'formOfOrganization' => $dto->formOfOrganization,
					'is_our'             => $dto->is_our,
					'is_competitor'      => $dto->is_competitor,
					'dealDate'           => $dto->dealDate,
				])
			);

			$this->eventManager->trigger(new CreateRequestDealEvent($request, $deal, $dto));

			$tx->commit();

			return $deal;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CreateDealDto $dto): Deal
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$model = new Deal([
				'status'                => DealStatusEnum::ACTIVE,
				'request_id'            => $dto->request->id,
				'consultant_id'         => $dto->consultant->id,
				'object_id'             => $dto->object->id,
				'complex_id'            => $dto->complex->id ?? null,
				'company_id'            => $dto->company->id ?? null,
				'type_id'               => $dto->type_id,
				'original_id'           => $dto->original_id,
				'visual_id'             => $dto->visual_id,
				'clientLegalEntity'     => $dto->clientLegalEntity,
				'description'           => $dto->description,
				'name'                  => $dto->name,
				'area'                  => $dto->area,
				'floorPrice'            => $dto->floorPrice,
				'competitor_company_id' => $dto->competitor->id ?? null,
				'contractTerm'          => $dto->contractTerm,
				'formOfOrganization'    => $dto->formOfOrganization,
				'is_our'                => $dto->is_our,
				'is_competitor'         => $dto->is_competitor,
				'dealDate'              => DateTimeHelper::tryFormat($dto->dealDate),
			]);

			$model->saveOrThrow();

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(Deal $deal, UpdateDealDto $dto): Deal
	{
		$deal->load([
			'request_id'            => $dto->request->id,
			'consultant_id'         => $dto->consultant->id,
			'object_id'             => $dto->object->id,
			'complex_id'            => $dto->complex->id ?? null,
			'company_id'            => $dto->company->id ?? null,
			'type_id'               => $dto->type_id,
			'original_id'           => $dto->original_id,
			'visual_id'             => $dto->visual_id,
			'clientLegalEntity'     => $dto->clientLegalEntity,
			'description'           => $dto->description,
			'name'                  => $dto->name,
			'area'                  => $dto->area,
			'floorPrice'            => $dto->floorPrice,
			'competitor_company_id' => $dto->competitor->id ?? null,
			'contractTerm'          => $dto->contractTerm,
			'formOfOrganization'    => $dto->formOfOrganization,
			'is_our'                => $dto->is_our,
			'is_competitor'         => $dto->is_competitor,
			'dealDate'              => DateTimeHelper::tryFormat($dto->dealDate),
		]);

		$deal->saveOrThrow();

		return $deal;
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(Deal $deal): void
	{
		$deal->delete();
	}
}