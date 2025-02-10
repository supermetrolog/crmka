<?php

declare(strict_types=1);

namespace app\models\forms\Media;

use app\dto\Media\CreateMediaDto;
use app\dto\Media\DeleteMediaDto;
use app\kernel\common\models\Form\Form;
use app\models\Media;
use yii\web\UploadedFile;

class MediaForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_DELETE = 'scenario_delete';

	/**
	 * @var UploadedFile[]
	 */
	public $files;
	public $file;
	public $category;
	public $model_type;
	public $model_id;

	public $media_id;
	public $media_ids = [];

	public function rules(): array
	{
		return [
			[['category', 'model_type', 'model_id'], 'required'],
			[['files'], 'each', 'rule' => ['file'], 'skipOnEmpty' => true],
			['file', 'file', 'skipOnEmpty' => true],
			['media_ids', 'each', 'rule' => [
				'exist',
				'targetClass'     => Media::class,
				'targetAttribute' => ['media_ids' => 'id'],
				'filter'          => ['deleted_at' => null]
			]],
			['media_id', 'exist', 'targetClass' => Media::class, 'targetAttribute' => ['media_id' => 'id'], 'filter' => ['deleted_at' => null]],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'files',
			'file',
			'category',
			'model_type',
			'model_id',
		];

		return [
			self::SCENARIO_CREATE => [...$common],
			self::SCENARIO_DELETE => ['media_id', 'media_ids']
		];
	}

	public function getDtos(): array
	{
		$dtos = [];

		if ($this->getScenario() === self::SCENARIO_CREATE) {
			foreach ($this->files as $file) {
				$dtos[] = new CreateMediaDto([
					'category'     => $this->category,
					'model_type'   => $this->model_type,
					'model_id'     => $this->model_id,
					'uploadedFile' => $file,
					'mime_type'    => mime_content_type($file->tempName),
				]);
			}
		} else {
			foreach ($this->media_ids as $media_id) {
				$dtos[] = new DeleteMediaDto([
					'mediaId' => $media_id
				]);
			}
		}

		return $dtos;
	}

	/**
	 * @return CreateMediaDto|DeleteMediaDto
	 */
	public function getDto()
	{
		if ($this->getScenario() === self::SCENARIO_CREATE) {
			return new CreateMediaDto([
				'category'     => $this->category,
				'model_type'   => $this->model_type,
				'model_id'     => $this->model_id,
				'uploadedFile' => $this->file,
				'mime_type'    => mime_content_type($this->file->tempName),
			]);
		}

		return new DeleteMediaDto([
			'mediaId' => $this->media_id
		]);
	}
}