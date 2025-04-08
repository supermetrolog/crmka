<?php

declare(strict_types=1);

namespace app\models\forms\Timeline;

use app\dto\Timeline\CreateTimelineDto;
use app\kernel\common\models\Form\Form;
use app\models\Request;
use app\models\User;
use Exception;

class TimelineForm extends Form
{
	public $request_id;
	public $consultant_id;

	public function rules(): array
	{
		return [
			[['request_id', 'consultant_id'], 'integer'],
			[['request_id', 'consultant_id'], 'required'],
			[['request_id'], 'exist', 'targetClass' => Request::class, 'targetAttribute' => ['request_id' => 'id']],
			[['consultant_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['consultant_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'request_id'    => 'ID запроса',
			'consultant_id' => 'ID консультанта',
		];
	}

	/**
	 * @throws Exception
	 */
	public function getDto(): CreateTimelineDto
	{
		return new CreateTimelineDto([
			'request_id'    => $this->request_id,
			'consultant_id' => $this->consultant_id,
		]);
	}
}