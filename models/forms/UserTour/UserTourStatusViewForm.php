<?php

declare(strict_types=1);

namespace app\models\forms\UserTour;

use app\kernel\common\models\Form\Form;

class UserTourStatusViewForm extends Form
{
	public $tour_id;

	public function rules(): array
	{
		return [
			['tour_id', 'string', 'max' => 64]
		];
	}
}