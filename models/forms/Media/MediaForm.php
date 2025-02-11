<?php

declare(strict_types=1);

namespace app\models\forms\Media;

use app\dto\Media\CreateMediaDto;
use app\kernel\common\models\Form\Form;
use yii\web\UploadedFile;

class MediaForm extends Form
{
	/**
	 * @var UploadedFile[]
	 */
	public $files;
	public $file;
	public $category;
	public $model_type;
	public $model_id;

	public function rules(): array
	{
		return [
			[['category', 'model_type', 'model_id'], 'required'],
			[['files'], 'each', 'rule' => ['file'], 'skipOnEmpty' => true],
			['file', 'file', 'skipOnEmpty' => true]
		];
	}

	/**
	 * @return CreateMediaDto[]
	 */
	public function getDtos(): array
	{
		$dtos = [];

		foreach ($this->files as $file) {
			$dtos[] = new CreateMediaDto([
				'category'     => $this->category,
				'model_type'   => $this->model_type,
				'model_id'     => $this->model_id,
				'uploadedFile' => $file,
				'mime_type'    => mime_content_type($file->tempName),
			]);
		}

		return $dtos;
	}

	public function getDto(): CreateMediaDto
	{
		return new CreateMediaDto([
			'category'     => $this->category,
			'model_type'   => $this->model_type,
			'model_id'     => $this->model_id,
			'uploadedFile' => $this->file,
			'mime_type'    => mime_content_type($this->file->tempName),
		]);
	}
}