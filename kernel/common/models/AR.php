<?php

declare(strict_types=1);

namespace app\kernel\common\models;

use app\exceptions\domain\model\SaveModelException;
use Exception;
use Throwable;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class AR extends ActiveRecord
{
	/**
	 * @throws Exception
	 */
	public function getAnyError(): ?string
	{
		return ArrayHelper::getValue($this->getFirstErrors(), 0);
	}

	/**
	 * @throws SaveModelException
	 */
	public function saveOrThrow(bool $runValidation = true): void
	{
		try {
			if (!$this->save($runValidation)) {
				throw new SaveModelException($this);
			}
		} catch (Throwable $th) {
			throw new SaveModelException($this, $th);
		}
	}
}