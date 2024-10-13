<?php

declare(strict_types=1);

namespace app\usecases\OfferMix;

use app\helpers\DateTimeHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\OfferMix;
use Throwable;

class OfferMixService
{
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @param OfferMix $model
	 *
	 * @return bool
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function toggleAvitoAd(OfferMix $model): bool
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$block = $model->block;

			if (!$block) {
				throw new ModelNotFoundException("Offer block not found");
			}

			$adIsActive = !$block->ad_avito;

			$block->ad_avito = $adIsActive;
			$model->ad_avito = $adIsActive;

			$block->ad_avito_date_start = $adIsActive ? DateTimeHelper::nowf() : null;

			$model->saveOrThrow();
			$block->saveOrThrow();

			$tx->commit();

			return $adIsActive;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @param OfferMix $model
	 *
	 * @return bool
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function toggleIsFake(OfferMix $model): bool
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$block = $model->block;

			if (!$block) {
				throw new ModelNotFoundException("Offer block not found");
			}

			$isFake = !$block->is_fake;

			$block->is_fake = $isFake;
			$model->is_fake = $isFake;

			$model->saveOrThrow();
			$block->saveOrThrow();

			$tx->commit();

			return $isFake;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}
}