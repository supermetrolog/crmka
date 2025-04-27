<?php

declare(strict_types=1);

namespace app\models\forms\Folder;

use app\dto\Folder\ReorderFolderDto;
use app\helpers\ArrayHelper;
use app\kernel\common\models\Form\Form;
use app\models\Folder;

class FoldersReorderForm extends Form
{
	public $folder_ids;

	public function rules(): array
	{
		return [
			[['folder_ids'], 'required'],
			['folder_ids', 'each', 'rule' => ['integer']],
			['folder_ids', 'each', 'rule' => [
				'exist',
				'targetClass'     => Folder::class,
				'targetAttribute' => ['folder_ids' => 'id']
			]],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'folder_ids' => 'ID папок'
		];
	}

	/**
	 * @return ReorderFolderDto[]
	 */
	public function getDtos(): array
	{
		$foldersOrdersMap = ArrayHelper::flip($this->folder_ids);
		$folders          = Folder::find()->byIds($this->folder_ids)->all();

		$dtos = [];

		foreach ($folders as $folder) {
			$dtos[] = new ReorderFolderDto([
				'folder'    => $folder,
				'sortOrder' => $foldersOrdersMap[$folder->id]
			]);
		}

		return $dtos;
	}
}