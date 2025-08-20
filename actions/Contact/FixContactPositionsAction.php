<?php

declare(strict_types=1);

namespace app\actions\Contact;

use app\kernel\common\actions\Action;
use app\models\Contact;

class FixContactPositionsAction extends Action
{
	private const OLD_POSITION_ID = 0;
	private const NEW_POSITION_ID = 17;

	public function run(): void
	{
		$this->infof('Start change old contact position_id from "%d" to "%d"', self::OLD_POSITION_ID, self::NEW_POSITION_ID);

		$query = Contact::find()->andWhere(['position_id' => self::OLD_POSITION_ID]);

		$count = 0;

		/** @var Contact $contact */
		foreach ($query->each() as $contact) {
			if ($contact->position_id !== self::OLD_POSITION_ID) {
				continue;
			}

			$contact->updateAttributes(['position_id' => self::NEW_POSITION_ID]);

			$this->commentf('Changed contact #%d', $contact->id);

			$count++;
		}

		if ($count > 0) {
			$this->infof('Complete. Changed position_id from "%d" to "%d" for %d contacts', self::OLD_POSITION_ID, self::NEW_POSITION_ID, $count);
		} else {
			$this->info('Complete. No contacts found');
		}
	}
}