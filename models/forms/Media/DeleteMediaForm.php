<?php

declare(strict_types=1);

namespace app\models\forms\Media;

use app\dto\Media\DeleteMediaDto;
use app\kernel\common\models\Form\Form;
use app\models\Media;

class DeleteMediaForm extends Form
{
	public $media_id;
	public $media_ids = [];

	public function rules(): array
	{
		return [
			['media_ids', 'each', 'rule' => [
				'exist',
				'targetClass'     => Media::class,
				'targetAttribute' => ['media_ids' => 'id'],
				'filter'          => ['deleted_at' => null]
			]],
			['media_id', 'exist', 'targetClass' => Media::class, 'targetAttribute' => ['media_id' => 'id'], 'filter' => ['deleted_at' => null]],
		];
	}

	/**
	 * @return DeleteMediaDto[]
	 */
	public function getDtos(): array
	{
		$dtos = [];

		foreach ($this->media_ids as $media_id) {
			$dtos[] = new DeleteMediaDto([
				'mediaId' => $media_id
			]);
		}

		return $dtos;
	}

	public function getDto(): DeleteMediaDto
	{
		return new DeleteMediaDto([
			'mediaId' => $this->media_id
		]);
	}
}