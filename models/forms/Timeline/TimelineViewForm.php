<?php

declare(strict_types=1);

namespace app\models\forms\Timeline;

use app\kernel\common\models\Form\Form;
use app\models\Request;
use app\models\User\User;

class TimelineViewForm extends Form
{
	public $request_id;
	public $consultant_id;

	public function rules(): array
	{
		return [
			[['request_id', 'consultant_id'], 'integer'],
			[['request_id', 'consultant_id'], 'required'],
			[['request_id'], 'exist', 'skipOnError' => true, 'targetClass' => Request::class, 'targetAttribute' => ['request_id' => 'id']],
			[['consultant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['consultant_id' => 'id']]
		];
	}

	public function attributeLabels(): array
	{
		return [
			'request_id'    => 'ID запроса',
			'consultant_id' => 'ID консультанта'
		];
	}
}