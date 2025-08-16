<?php

declare(strict_types=1);

namespace app\actions\Call;

use app\kernel\common\actions\Action;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Call;
use app\models\Contact;
use app\models\miniModels\Phone;
use yii\base\ErrorException;

class SetContactCallMainPhone extends Action
{
	/**
	 * @throws ErrorException
	 * @throws SaveModelException
	 */
	public function run(): void
	{
		$this->info('DataFix: Set main contact phone for old unhandled calls');

		$query = Call::find()
		             ->with(['contact.mainPhone'])
		             ->andWhere([Call::field('phone_id') => null])
		             ->andWhere(['IS NOT', Call::field('contact_id'), null]);

		$count = 0;

		/** @var Call $call */
		foreach ($query->each() as $call) {
			$phone = $this->getContactPhone($call->contact);

			if (!is_null($phone)) {
				$this->setCallPhone($call, $phone);

				$this->infof('Set Phone #%d for Call #%d', $call->phone_id, $call->id);
				$count++;
			}
		}

		$this->infof('Complete. Changed calls: %d', $count);
	}

	private function getContactPhone(Contact $contact): ?Phone
	{
		return $contact->mainPhone ?? $contact->phones[0] ?? null;
	}

	/**
	 * @throws SaveModelException
	 */
	private function setCallPhone(Call $call, Phone $phone): void
	{
		$call->phone_id = $phone->id;
		$call->saveOrThrow();
	}
}