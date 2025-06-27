<?php

declare(strict_types=1);

namespace app\models\forms\UserTour;

use app\dto\UserTour\UserTourViewDto;
use app\kernel\common\models\Form\Form;
use app\models\User;
use Exception;

class UserTourViewForm extends Form
{
	public $user_id;
	public $tour_id;
	public $steps_viewed;
	public $steps_total;

	public function rules(): array
	{
		return [
			[['user_id', 'tour_id', 'steps_total', 'steps_viewed'], 'required'],
			[['user_id', 'steps_total', 'steps_viewed'], 'integer'],
			['tour_id', 'string', 'max' => 64],
			['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * @throws Exception
	 */
	public function getDto(): UserTourViewDto
	{
		return new UserTourViewDto([
			'user'         => User::find()->byId((int)$this->user_id)->oneOrThrow(),
			'tour_id'      => $this->tour_id,
			'steps_viewed' => $this->steps_viewed,
			'steps_total'  => $this->steps_total
		]);
	}
}